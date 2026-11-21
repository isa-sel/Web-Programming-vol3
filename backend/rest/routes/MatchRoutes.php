<?php

declare(strict_types=1);

Flight::route('GET /matches', function () {
    Flight::json(Flight::match_service()->get_all());
});

Flight::route('GET /matches/@id:[0-9]+', function ($id) {
    Flight::json(Flight::match_service()->get_by_id($id));
});

/** Body: { "home_team_id": 1, "away_team_id": 2, "venue_id": 1, "match_date": "2026-12-05 18:00:00", "status": "scheduled" } */
Flight::route('POST /matches', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::match_service()->add($data), 201);
});

Flight::route('PUT /matches/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::match_service()->update($id, $data));
});

Flight::route('DELETE /matches/@id:[0-9]+', function ($id) {
    Flight::match_service()->delete($id);
    Flight::json(['message' => 'Match deleted.']);
});

/* External API: the weather at the venue at the time of the match (Open-Meteo) */
Flight::route('GET /matches/@id:[0-9]+/weather', function ($id) {
    Flight::json(Flight::weather_service()->get_for_match($id));
});
