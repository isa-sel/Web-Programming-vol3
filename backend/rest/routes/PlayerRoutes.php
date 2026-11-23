<?php

declare(strict_types=1);

Flight::route('GET /players', function () {
    Flight::json(Flight::player_service()->get_all());
});

/* All players with the name and city of their team (JOIN) */
Flight::route('GET /players/with-team', function () {
    Flight::json(Flight::player_service()->get_all_with_team());
});

Flight::route('GET /players/@id:[0-9]+', function ($id) {
    Flight::json(Flight::player_service()->get_by_id($id));
});

/** Body: { "first_name": "Kenan", "last_name": "Hodžić", "birth_date": "2003-05-14", "position": "Pivot", "jersey_number": 22, "team_id": 1 } */
Flight::route('POST /players', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::player_service()->add($data), 201);
});

Flight::route('PUT /players/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::player_service()->update($id, $data));
});

Flight::route('DELETE /players/@id:[0-9]+', function ($id) {
    Flight::player_service()->delete($id);
    Flight::json(['message' => 'Player deleted.']);
});
