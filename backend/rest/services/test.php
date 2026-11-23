<?php

declare(strict_types=1);

/*
 * Tries to break every rule of every service and prints what happened.
 * Run it from the project root after importing database/rukometni_centar.sql:
 *     php backend/rest/services/test.php
 * Everything the script adds is deleted again, so it can be run many times.
 */

require_once __DIR__ . '/RoleService.php';
require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/TeamService.php';
require_once __DIR__ . '/PlayerService.php';
require_once __DIR__ . '/VenueService.php';
require_once __DIR__ . '/MatchService.php';
require_once __DIR__ . '/StandingService.php';

/** Run one action and print OK or the exception it threw. */
function attempt(string $description, callable $action)
{
    echo str_pad($description, 55, '.') . ' ';
    try {
        $result = $action();
        echo 'OK' . (isset($result['id']) ? " (id {$result['id']})" : '') . "\n";

        return $result;
    } catch (HttpException $e) {
        echo get_class($e) . " [{$e->get_status()}]: {$e->getMessage()}\n";

        return null;
    }
}

$roles = new RoleService();
$users = new UserService();
$teams = new TeamService();
$players = new PlayerService();
$venues = new VenueService();
$matches = new MatchService();
$standings = new StandingService();

$nextYear = (int) date('Y') + 1;
$future = $nextYear . '-03-14 18:00:00';

echo "=== Read ===\n";
foreach (['Roles' => $roles, 'Users' => $users, 'Teams' => $teams, 'Players' => $players,
          'Venues' => $venues, 'Matches' => $matches, 'Standings' => $standings] as $name => $service) {
    echo str_pad($name, 12) . count($service->get_all()) . "\n";
}
attempt('get team #999', fn () => $teams->get_by_id(999));

echo "\n=== RoleService ===\n";
attempt('add: name "Editor" (uppercase)', fn () => $roles->add(['name' => 'Editor']));
attempt('add: name "admin" (exists)', fn () => $roles->add(['name' => 'admin']));
attempt('update #1: keeps its own name "admin"', fn () => $roles->update(1, ['name' => 'admin', 'description' => 'Full league administration access']));
attempt('delete #1 (has users)', fn () => $roles->delete(1));
$role = attempt('add: "tester"', fn () => $roles->add(['name' => 'tester']));
attempt('delete "tester"', fn () => $roles->delete($role['id']));

echo "\n=== UserService ===\n";
$newUser = ['first_name' => 'Test', 'last_name' => 'User', 'email' => 'Test.User@Example.com'];
attempt('add: no password', fn () => $users->add($newUser));
attempt('add: password "abc"', fn () => $users->add($newUser + ['password' => 'abc']));
attempt('add: e-mail "ADMIN@rukometnicentar.com" (exists)', fn () => $users->add(['email' => 'ADMIN@rukometnicentar.com', 'password' => 'Secret123'] + $newUser));
attempt('add: role #999', fn () => $users->add($newUser + ['password' => 'Secret123', 'role_id' => 999]));
$user = attempt('add: valid user without a role', fn () => $users->add($newUser + ['password' => 'Secret123']));
if ($user !== null) {
    echo "  stored e-mail: {$user['email']}, role_id: {$user['role_id']}, hash returned: " . (isset($user['password_hash']) ? 'yes' : 'no') . "\n";
    $stored = (new UserDao())->get_by_email($user['email']);
    echo '  password stored as bcrypt hash: ' . (password_verify('Secret123', $stored['password_hash']) ? 'yes' : 'no') . "\n";
    attempt('delete the new user', fn () => $users->delete($user['id']));
}

echo "\n=== TeamService ===\n";
attempt('add: no city', fn () => $teams->add(['name' => 'RK Test', 'category' => 'Senior Men']));
attempt('add: category "Veterans"', fn () => $teams->add(['name' => 'RK Test', 'city' => 'Tuzla', 'category' => 'Veterans']));
attempt('add: founded in 3000', fn () => $teams->add(['name' => 'RK Test', 'city' => 'Tuzla', 'category' => 'Senior Men', 'founded_year' => 3000]));
attempt('add: "RK Bosna" in Sarajevo (exists)', fn () => $teams->add(['name' => 'RK Bosna', 'city' => 'Sarajevo', 'category' => 'Senior Men']));
attempt('delete #1 (has players, matches, standings)', fn () => $teams->delete(1));
$team = attempt('add: "RK Test" in Tuzla', fn () => $teams->add(['name' => 'RK Test', 'city' => 'Tuzla', 'category' => 'Senior Men', 'founded_year' => 1999]));

echo "\n=== PlayerService ===\n";
$newPlayer = ['first_name' => 'Kenan', 'last_name' => 'Test', 'position' => 'Pivot', 'jersey_number' => 22, 'team_id' => $team['id'] ?? 0];
attempt('add: jersey 100', fn () => $players->add(['jersey_number' => 100] + $newPlayer));
attempt('add: position "Striker"', fn () => $players->add(['position' => 'Striker'] + $newPlayer));
attempt('add: team #999', fn () => $players->add(['team_id' => 999] + $newPlayer));
attempt('add: born yesterday', fn () => $players->add(['birth_date' => date('Y-m-d', strtotime('-1 day'))] + $newPlayer));
attempt('add: jersey 10 in RK Bosna (taken)', fn () => $players->add(['jersey_number' => 10, 'team_id' => 1] + $newPlayer));
$player = attempt('add: valid player', fn () => $players->add(['birth_date' => '2003-05-14'] + $newPlayer));
attempt('update: keeps its own jersey number', fn () => $players->update($player['id'], ['last_name' => 'Tested'] + $newPlayer));
attempt('players of team #999', fn () => $players->get_by_team(999));
echo '  players of the new team: ' . count($players->get_by_team($team['id'])) . "\n";

echo "\n=== VenueService ===\n";
$newVenue = ['name' => 'Dvorana Test', 'city' => 'Tuzla', 'address' => 'Testna 1, Tuzla', 'capacity' => 1500];
attempt('add: capacity 0', fn () => $venues->add(['capacity' => 0] + $newVenue));
attempt('add: name with <script>', fn () => $venues->add(['name' => '<script>alert(1)</script>'] + $newVenue));
attempt('add: "Dvorana Skenderija" in Sarajevo (exists)', fn () => $venues->add(['name' => 'Dvorana Skenderija', 'city' => 'Sarajevo'] + $newVenue));
attempt('delete #1 (hosts matches)', fn () => $venues->delete(1));
$venue = attempt('add: valid venue', fn () => $venues->add($newVenue));

echo "\n=== MatchService ===\n";
$newMatch = ['home_team_id' => 1, 'away_team_id' => $team['id'], 'venue_id' => $venue['id'], 'match_date' => $future];
attempt('add: team plays against itself', fn () => $matches->add(['away_team_id' => 1] + $newMatch));
attempt('add: venue #999', fn () => $matches->add(['venue_id' => 999] + $newMatch));
attempt('add: scheduled in 2020', fn () => $matches->add(['match_date' => '2020-01-01 18:00:00'] + $newMatch));
attempt('add: date "tomorrow"', fn () => $matches->add(['match_date' => 'tomorrow'] + $newMatch));
attempt('add: status "finished"', fn () => $matches->add(['status' => 'finished'] + $newMatch));
attempt('add: completed without scores', fn () => $matches->add(['status' => 'completed'] + $newMatch));
attempt('add: scheduled with scores', fn () => $matches->add(['home_score' => 30, 'away_score' => 25] + $newMatch));
$match = attempt('add: valid scheduled match (HTML date format)', fn () => $matches->add(['match_date' => substr($future, 0, 10) . 'T18:00'] + $newMatch));
attempt('update: completed 30:25', fn () => $matches->update($match['id'], ['status' => 'completed', 'home_score' => 30, 'away_score' => 25] + $newMatch));
attempt('delete the new venue (hosts a match)', fn () => $venues->delete($venue['id']));

echo "\n=== StandingService ===\n";
$newStanding = ['team_id' => $team['id'], 'season' => '2027-2028', 'wins' => 3, 'draws' => 1, 'losses' => 2,
                'goals_for' => 150, 'goals_against' => 140, 'position' => 6];
attempt('add: season "2027"', fn () => $standings->add(['season' => '2027'] + $newStanding));
attempt('add: season "2027-2029"', fn () => $standings->add(['season' => '2027-2029'] + $newStanding));
attempt('add: wins -1', fn () => $standings->add(['wins' => -1] + $newStanding));
attempt('add: RK Bosna in 2026-2027 (exists)', fn () => $standings->add(['team_id' => 1, 'season' => '2026-2027'] + $newStanding));
attempt('add: position 1 in 2026-2027 (taken)', fn () => $standings->add(['season' => '2026-2027', 'position' => 1] + $newStanding));
$standing = attempt('add: valid row, client sends points 99', fn () => $standings->add(['played' => 50, 'points' => 99] + $newStanding));
if ($standing !== null) {
    echo "  played: {$standing['played']} (3+1+2), points: {$standing['points']} (3*2+1)\n";
}
attempt('season "abc"', fn () => $standings->get_by_season('abc'));
echo '  rows in 2026-2027: ' . count($standings->get_by_season('2026-2027')) . "\n";

echo "\n=== Clean-up ===\n";
attempt('delete the new team (still has data)', fn () => $teams->delete($team['id']));
attempt('delete the standings row', fn () => $standings->delete($standing['id']));
attempt('delete the match', fn () => $matches->delete($match['id']));
attempt('delete the player', fn () => $players->delete($player['id']));
attempt('delete the venue', fn () => $venues->delete($venue['id']));
attempt('delete the team', fn () => $teams->delete($team['id']));
attempt('delete the team again', fn () => $teams->delete($team['id']));
