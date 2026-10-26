<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?? '/';
$segments = array_values(array_filter(explode('/', strtolower(trim($path, '/'))), static fn ($segment) => $segment !== ''));

if ($segments !== [] && $segments[0] === 'api') {
    array_shift($segments);
}

$route = $segments[0] ?? 'health';

$teams = [
    [
        'id' => 1,
        'name' => 'RK Bosna',
        'city' => 'Sarajevo',
        'founded' => 1948,
        'players' => 23,
        'category' => 'Senior Men',
    ],
    [
        'id' => 2,
        'name' => 'HRK Izviđač',
        'city' => 'Ljubuški',
        'founded' => 1956,
        'players' => 21,
        'category' => 'Senior Men',
    ],
    [
        'id' => 3,
        'name' => 'RK Krivaja',
        'city' => 'Zavidovići',
        'founded' => 1952,
        'players' => 22,
        'category' => 'Senior Men',
    ],
];

$players = [
    [
        'id' => 1,
        'name' => 'Admir Mehmedović',
        'team' => 'RK Bosna',
        'position' => 'Srednji bek',
        'age' => 27,
        'number' => 10,
    ],
    [
        'id' => 2,
        'name' => 'Emir Preldžić',
        'team' => 'RK Krivaja',
        'position' => 'Desno krilo',
        'age' => 24,
        'number' => 7,
    ],
    [
        'id' => 3,
        'name' => 'Selma Hasanbegović',
        'team' => 'ŽRK Hadžići',
        'position' => 'Golman',
        'age' => 23,
        'number' => 1,
    ],
];

$matches = [
    [
        'id' => 1,
        'home_team' => 'RK Bosna',
        'away_team' => 'HRK Izviđač',
        'date' => '2025-03-24',
        'time' => '18:00',
        'venue' => 'Dvorana Skenderija',
        'status' => 'upcoming',
    ],
    [
        'id' => 2,
        'home_team' => 'RK Krivaja',
        'away_team' => 'RK Sloboda',
        'date' => '2025-03-25',
        'time' => '19:30',
        'venue' => 'Gradska dvorana Zavidovići',
        'status' => 'upcoming',
    ],
];

$standings = [
    ['position' => 1, 'team' => 'RK Bosna', 'played' => 6, 'wins' => 5, 'draws' => 1, 'losses' => 0, 'goals_for' => 172, 'goals_against' => 145, 'points' => 11],
    ['position' => 2, 'team' => 'HRK Izviđač', 'played' => 6, 'wins' => 4, 'draws' => 1, 'losses' => 1, 'goals_for' => 168, 'goals_against' => 149, 'points' => 9],
    ['position' => 3, 'team' => 'RK Krivaja', 'played' => 6, 'wins' => 3, 'draws' => 2, 'losses' => 1, 'goals_for' => 162, 'goals_against' => 152, 'points' => 8],
];

$venues = [
    [
        'id' => 1,
        'name' => 'Dvorana Skenderija',
        'city' => 'Sarajevo',
        'address' => 'Terezija bb, Sarajevo',
        'capacity' => 6000,
    ],
    [
        'id' => 2,
        'name' => 'Gradska dvorana Zavidovići',
        'city' => 'Zavidovići',
        'address' => 'Radnička 3, Zavidovići',
        'capacity' => 2500,
    ],
    [
        'id' => 3,
        'name' => 'Sportska dvorana Hadžići',
        'city' => 'Hadžići',
        'address' => 'Zlatnih ljiljana 24, Hadžići',
        'capacity' => 1800,
    ],
];

$datasets = [
    'health' => ['status' => 'ok', 'service' => 'Rukometni Centar API'],
    'teams' => $teams,
    'players' => $players,
    'matches' => $matches,
    'standings' => $standings,
    'venues' => $venues,
];

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJson(['error' => 'Method not allowed'], 405);
    exit;
}

$data = $datasets[$route] ?? null;

if ($data === null) {
    sendJson(['error' => 'Route not found', 'available_routes' => array_keys($datasets)], 404);
    exit;
}

sendJson(['success' => true, 'data' => $data]);
