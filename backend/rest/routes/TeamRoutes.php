<?php

declare(strict_types=1);

Flight::route('GET /teams', function () {
    Flight::json(Flight::team_service()->get_all());
});

Flight::route('GET /teams/@id:[0-9]+', function ($id) {
    Flight::json(Flight::team_service()->get_by_id($id));
});

/** Body: { "name": "RK Sloboda", "city": "Tuzla", "founded_year": 1955, "category": "Senior Men" } */
Flight::route('POST /teams', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::team_service()->add($data), 201);
});

Flight::route('PUT /teams/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::team_service()->update($id, $data));
});

Flight::route('DELETE /teams/@id:[0-9]+', function ($id) {
    Flight::team_service()->delete($id);
    Flight::json(['message' => 'Team deleted.']);
});

/* The players of one team, ordered by jersey number */
Flight::route('GET /teams/@id:[0-9]+/players', function ($id) {
    Flight::json(Flight::player_service()->get_by_team($id));
});

/* The home and away matches of one team */
Flight::route('GET /teams/@id:[0-9]+/matches', function ($id) {
    Flight::json(Flight::match_service()->get_by_team($id));
});
