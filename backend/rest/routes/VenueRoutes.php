<?php

declare(strict_types=1);

Flight::route('GET /venues', function () {
    Flight::json(Flight::venue_service()->get_all());
});

Flight::route('GET /venues/@id:[0-9]+', function ($id) {
    Flight::json(Flight::venue_service()->get_by_id($id));
});

/** Body: { "name": "Dvorana Mejdan", "city": "Tuzla", "address": "Rudarska 1, Tuzla", "capacity": 4500 } */
Flight::route('POST /venues', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::venue_service()->add($data), 201);
});

Flight::route('PUT /venues/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::venue_service()->update($id, $data));
});

Flight::route('DELETE /venues/@id:[0-9]+', function ($id) {
    Flight::venue_service()->delete($id);
    Flight::json(['message' => 'Venue deleted.']);
});
