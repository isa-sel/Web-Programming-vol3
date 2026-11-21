<?php

declare(strict_types=1);

Flight::route('GET /users', function () {
    Flight::json(Flight::user_service()->get_all());
});

Flight::route('GET /users/@id:[0-9]+', function ($id) {
    Flight::json(Flight::user_service()->get_by_id($id));
});

/** Body: { "first_name": "Ena", "last_name": "Begić", "email": "ena@example.com", "password": "Rukomet2026", "role_id": 3, "is_active": 1 } */
Flight::route('POST /users', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_service()->add($data), 201);
});

Flight::route('PUT /users/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_service()->update($id, $data));
});

Flight::route('DELETE /users/@id:[0-9]+', function ($id) {
    Flight::user_service()->delete($id);
    Flight::json(['message' => 'User deleted.']);
});
