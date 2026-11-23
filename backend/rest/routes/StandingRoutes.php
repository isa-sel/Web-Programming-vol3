<?php

declare(strict_types=1);

/* /standings?season=2026-2027 returns the table of one season, ordered by position */
Flight::route('GET /standings', function () {
    $season = Flight::request()->query['season'] ?? null;
    if ($season !== null) {
        Flight::json(Flight::standing_service()->get_by_season($season));
        return;
    }
    Flight::json(Flight::standing_service()->get_all());
});

Flight::route('GET /standings/@id:[0-9]+', function ($id) {
    Flight::json(Flight::standing_service()->get_by_id($id));
});

/** Body: { "team_id": 1, "season": "2027-2028", "wins": 5, "draws": 1, "losses": 0, "goals_for": 172, "goals_against": 145, "position": 1 } */
Flight::route('POST /standings', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::standing_service()->add($data), 201);
});

Flight::route('PUT /standings/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::standing_service()->update($id, $data));
});

Flight::route('DELETE /standings/@id:[0-9]+', function ($id) {
    Flight::standing_service()->delete($id);
    Flight::json(['message' => 'Standing deleted.']);
});
