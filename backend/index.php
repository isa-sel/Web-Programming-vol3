<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php'; // FlightPHP and swagger-php (installed by Composer)

require_once __DIR__ . '/utils/exceptions.php';
require_once __DIR__ . '/utils/Logger.php';
require_once __DIR__ . '/rest/services/RoleService.php';
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/TeamService.php';
require_once __DIR__ . '/rest/services/PlayerService.php';
require_once __DIR__ . '/rest/services/VenueService.php';
require_once __DIR__ . '/rest/services/MatchService.php';
require_once __DIR__ . '/rest/services/StandingService.php';
require_once __DIR__ . '/rest/services/WeatherService.php';

// Register every service ONCE; routes call e.g. Flight::team_service()
Flight::register('role_service', 'RoleService');
Flight::register('user_service', 'UserService');
Flight::register('team_service', 'TeamService');
Flight::register('player_service', 'PlayerService');
Flight::register('venue_service', 'VenueService');
Flight::register('match_service', 'MatchService');
Flight::register('standing_service', 'StandingService');
Flight::register('weather_service', 'WeatherService');

/* ONE place that turns every exception into a JSON answer: {"error": "..."} */
Flight::map('error', function (Throwable $e) {
    if ($e instanceof HttpException) { // thrown by a service: 400, 404, 503, ...
        $status = $e->get_status();
        $message = $e->getMessage();
    } else { // PDOException, a bug, ...
        $status = 500;
        $message = 'Something went wrong on the server. Please try again later.';
        // The details go only to the error log, never to the client
        Logger::error(sprintf(
            '%s %s 500 %s: %s (%s:%d)',
            Flight::request()->method,
            Flight::request()->url,
            get_class($e),
            $e->getMessage(),
            basename($e->getFile()),
            $e->getLine()
        ));
    }

    Flight::response()
        ->clearBody()
        ->status($status)
        ->header('Content-Type', 'application/json; charset=utf-8')
        ->write(json_encode(['error' => $message], JSON_UNESCAPED_UNICODE))
        ->send();
});

/* A URL that matches no route */
Flight::map('notFound', function () {
    Flight::response()
        ->clearBody()
        ->status(404)
        ->header('Content-Type', 'application/json; charset=utf-8')
        ->write(json_encode(['error' => 'Endpoint not found.']))
        ->send();
});

/* A URL that exists, but not with this HTTP method (e.g. PUT /teams) */
Flight::map('methodNotFound', function ($route) {
    Flight::response()
        ->clearBody()
        ->status(405)
        ->header('Content-Type', 'application/json; charset=utf-8')
        ->header('Allow', implode(', ', $route->methods))
        ->write(json_encode(['error' => 'Method not allowed. Allowed methods: ' . implode(', ', $route->methods) . '.']))
        ->send();
});

Flight::route('GET /', function () {
    Flight::json(['app' => 'Rukometni Centar API', 'message' => 'The backend is running.']);
});

require_once __DIR__ . '/rest/routes/RoleRoutes.php';
require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/TeamRoutes.php';
require_once __DIR__ . '/rest/routes/PlayerRoutes.php';
require_once __DIR__ . '/rest/routes/VenueRoutes.php';
require_once __DIR__ . '/rest/routes/MatchRoutes.php';
require_once __DIR__ . '/rest/routes/StandingRoutes.php';

Flight::start(); // always the LAST line
