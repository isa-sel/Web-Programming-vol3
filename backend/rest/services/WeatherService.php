<?php

declare(strict_types=1);

require_once __DIR__ . '/../../utils/exceptions.php';
require_once __DIR__ . '/../../utils/Validator.php';
require_once __DIR__ . '/../../utils/Logger.php';
require_once __DIR__ . '/../dao/MatchDao.php';
require_once __DIR__ . '/../dao/VenueDao.php';

/**
 * External API: the weather at the venue of a match, from Open-Meteo (https://open-meteo.com).
 *
 * 1. Geocoding API: the venue city -> latitude and longitude.
 * 2. Forecast API: the hourly weather on the match day; the hour of the throw-off is picked.
 *
 * Every call has a timeout. A failure is written to the error log and answered with
 * 503 and a friendly message, so the rest of the application keeps working.
 */
class WeatherService
{
    /** The forecast API covers about 3 months back and 16 days ahead. */
    public const PAST_DAYS = 92;
    public const FORECAST_DAYS = 15;

    public const UNAVAILABLE_MESSAGE = 'Weather information is not available at the moment. Please try again later.';

    /** WMO weather codes used by Open-Meteo. */
    private const DESCRIPTIONS = [
        0 => 'Clear sky', 1 => 'Mainly clear', 2 => 'Partly cloudy', 3 => 'Overcast',
        45 => 'Fog', 48 => 'Freezing fog',
        51 => 'Light drizzle', 53 => 'Drizzle', 55 => 'Dense drizzle',
        56 => 'Freezing drizzle', 57 => 'Dense freezing drizzle',
        61 => 'Light rain', 63 => 'Rain', 65 => 'Heavy rain',
        66 => 'Freezing rain', 67 => 'Heavy freezing rain',
        71 => 'Light snow', 73 => 'Snow', 75 => 'Heavy snow', 77 => 'Snow grains',
        80 => 'Light rain showers', 81 => 'Rain showers', 82 => 'Violent rain showers',
        85 => 'Snow showers', 86 => 'Heavy snow showers',
        95 => 'Thunderstorm', 96 => 'Thunderstorm with hail', 99 => 'Thunderstorm with heavy hail',
    ];

    private $matchDao;
    private $venueDao;

    public function __construct($matchDao = null, $venueDao = null)
    {
        $this->matchDao = $matchDao ?? new MatchDao();
        $this->venueDao = $venueDao ?? new VenueDao();
        BaseDao::loadConfig(); // the Config class from config.php or config.example.php
    }

    public function get_for_match($matchId): array
    {
        Validator::integer($matchId, 'id', 1);
        $match = $this->matchDao->get_by_id((int) $matchId);
        if ($match === null) {
            throw new NotFoundException('Match not found.');
        }
        $venue = $this->venueDao->get_by_id((int) $match['venue_id']);
        if ($venue === null) {
            throw new NotFoundException('Venue not found.');
        }

        // Rule: Open-Meteo only has data for a limited window around today
        $matchDay = substr($match['match_date'], 0, 10);
        $first = date('Y-m-d', strtotime('-' . self::PAST_DAYS . ' days'));
        $last = date('Y-m-d', strtotime('+' . self::FORECAST_DAYS . ' days'));
        if ($matchDay < $first || $matchDay > $last) {
            throw new ValidationException(
                'Weather is only available for matches from ' . self::PAST_DAYS . ' days ago to ' . self::FORECAST_DAYS . ' days ahead.'
            );
        }

        $location = $this->find_location($venue['city']);
        $hourly = $this->fetch_hourly($location, $matchDay);

        // The hour of the throw-off, e.g. 2026-11-07T19:00
        $hour = substr($match['match_date'], 0, 13) . ':00';
        $index = array_search(str_replace(' ', 'T', $hour), $hourly['time'] ?? [], true);
        if ($index === false) {
            Logger::error("Open-Meteo forecast has no data for $hour (match {$match['id']})");
            throw new ExternalServiceException(self::UNAVAILABLE_MESSAGE);
        }

        $code = isset($hourly['weather_code'][$index]) ? (int) $hourly['weather_code'][$index] : null;

        return [
            'match_id' => (int) $match['id'],
            'match_date' => $match['match_date'],
            'venue' => ['id' => (int) $venue['id'], 'name' => $venue['name'], 'city' => $venue['city']],
            'location' => $location,
            'weather' => [
                'time' => $hour,
                'temperature_c' => $hourly['temperature_2m'][$index] ?? null,
                'precipitation_mm' => $hourly['precipitation'][$index] ?? null,
                'precipitation_probability' => $hourly['precipitation_probability'][$index] ?? null,
                'wind_speed_kmh' => $hourly['wind_speed_10m'][$index] ?? null,
                'weather_code' => $code,
                'description' => self::DESCRIPTIONS[$code] ?? 'Unknown',
            ],
            'source' => 'Open-Meteo',
        ];
    }

    /** City name -> coordinates with the Open-Meteo Geocoding API. */
    private function find_location(string $city): array
    {
        $data = $this->request(Config::WEATHER_GEOCODING_URL(), [
            'name' => $city,
            'count' => 1,
            'language' => 'en',
            'format' => 'json',
            'countryCode' => Config::WEATHER_COUNTRY_CODE(),
        ], 'geocoding');

        $place = $data['results'][0] ?? null;
        if (!isset($place['latitude'], $place['longitude'])) {
            Logger::error("Open-Meteo geocoding found no location for city '$city'");
            throw new ExternalServiceException("The location of '$city' could not be found for the weather forecast.");
        }

        return [
            'name' => $place['name'] ?? $city,
            'latitude' => $place['latitude'],
            'longitude' => $place['longitude'],
        ];
    }

    private function fetch_hourly(array $location, string $day): array
    {
        $data = $this->request(Config::WEATHER_FORECAST_URL(), [
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'hourly' => 'temperature_2m,precipitation,precipitation_probability,weather_code,wind_speed_10m',
            'timezone' => 'auto',
            'start_date' => $day,
            'end_date' => $day,
        ], 'forecast');

        if (!isset($data['hourly']['time']) || !is_array($data['hourly']['time'])) {
            Logger::error('Open-Meteo forecast returned no hourly data');
            throw new ExternalServiceException(self::UNAVAILABLE_MESSAGE);
        }

        return $data['hourly'];
    }

    /** GET request with a timeout; every call is logged, every failure becomes a 503. */
    private function request(string $url, array $query, string $name): array
    {
        $fullUrl = $url . '?' . http_build_query($query);
        $started = microtime(true);

        $curl = curl_init($fullUrl);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => Config::WEATHER_TIMEOUT_SECONDS(),
            CURLOPT_TIMEOUT => Config::WEATHER_TIMEOUT_SECONDS(),
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $body = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        $milliseconds = (int) round((microtime(true) - $started) * 1000);

        if ($body === false) {
            Logger::error("Open-Meteo $name request failed after {$milliseconds}ms: $error");
            throw new ExternalServiceException(self::UNAVAILABLE_MESSAGE);
        }

        $data = json_decode((string) $body, true);
        if ($status !== 200 || !is_array($data)) {
            $reason = is_array($data) && isset($data['reason']) ? $data['reason'] : 'invalid response';
            Logger::error("Open-Meteo $name request answered $status after {$milliseconds}ms: $reason");
            throw new ExternalServiceException(self::UNAVAILABLE_MESSAGE);
        }

        Logger::info("External API Open-Meteo $name $status {$milliseconds}ms");

        return $data;
    }
}
