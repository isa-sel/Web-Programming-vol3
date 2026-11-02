<?php

declare(strict_types=1);

require_once __DIR__ . '/RoleDao.php';
require_once __DIR__ . '/UserDao.php';
require_once __DIR__ . '/TeamDao.php';
require_once __DIR__ . '/PlayerDao.php';
require_once __DIR__ . '/VenueDao.php';
require_once __DIR__ . '/MatchDao.php';
require_once __DIR__ . '/StandingDao.php';

function assertTest(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$roleDao = new RoleDao();
$userDao = new UserDao();
$teamDao = new TeamDao();
$playerDao = new PlayerDao();
$venueDao = new VenueDao();
$matchDao = new MatchDao();
$standingDao = new StandingDao();

foreach ([
    ['admin@rukometnicentar.com', 'admin123'],
    ['manager@rukometnicentar.com', 'manager123'],
    ['user@rukometnicentar.com', 'user123'],
] as [$email, $password]) {
    $sampleUser = $userDao->get_by_email($email);
    assertTest(
        $sampleUser !== null && password_verify($password, $sampleUser['password_hash']),
        'Sample account password verification failed for ' . $email . '.'
    );
}

foreach ([
    'roles' => $roleDao,
    'users' => $userDao,
    'teams' => $teamDao,
    'players' => $playerDao,
    'venues' => $venueDao,
    'matches' => $matchDao,
    'standings' => $standingDao,
] as $table => $dao) {
    assertTest(
        count($dao->get_all()) >= 2,
        'Expected several sample rows in the ' . $table . ' table.'
    );
}

$roleDao->begin_transaction();
$suffix = bin2hex(random_bytes(5));
$season = 'DAO-' . $suffix;

$roleId = $roleDao->add([
    'name' => 'dao_test_' . $suffix,
    'description' => 'Temporary DAO test role',
]);
assertTest($roleDao->get_by_id($roleId) !== null, 'Role read by ID failed.');
assertTest($roleDao->get_all() !== [], 'Role list returned no rows.');
assertTest(
    $roleDao->only_columns(['id', 'name']) === ['id', 'name'],
    'Role column selection failed.'
);
assertTest(
    $roleDao->update($roleId, ['description' => 'Updated DAO test role']),
    'Role update failed.'
);

$passwordHash = password_hash('dao-test-password', PASSWORD_BCRYPT);
$userId = $userDao->add([
    'first_name' => 'DAO',
    'last_name' => 'Tester',
    'email' => 'dao_' . $suffix . '@example.test',
    'password_hash' => $passwordHash,
    'role_id' => $roleId,
    'is_active' => 1,
]);
assertTest($userDao->get_by_id($userId) !== null, 'User read by ID failed.');
assertTest($userDao->get_all() !== [], 'User list returned no rows.');
assertTest(
    !array_key_exists('password_hash', $userDao->get_by_id($userId)),
    'User read by ID exposed the password hash.'
);
assertTest(
    !array_key_exists('password_hash', $userDao->get_all()[0]),
    'User list exposed the password hash.'
);
$passwordHashRejected = false;
try {
    $userDao->get_all(['id', 'password_hash']);
} catch (InvalidArgumentException) {
    $passwordHashRejected = true;
}
assertTest($passwordHashRejected, 'User list allowed explicitly selecting a password hash.');
$userByEmail = $userDao->get_by_email('dao_' . $suffix . '@example.test');
assertTest(
    $userByEmail !== null && password_verify('dao-test-password', $userByEmail['password_hash']),
    'User lookup by email failed.'
);
assertTest(
    $userDao->update($userId, ['last_name' => 'Updated']),
    'User update failed.'
);

$teamAId = $teamDao->add([
    'name' => 'DAO Test Home ' . $suffix,
    'city' => 'Sarajevo',
    'founded_year' => 2020,
    'category' => 'Senior Men',
]);
$teamBId = $teamDao->add([
    'name' => 'DAO Test Away ' . $suffix,
    'city' => 'Mostar',
    'founded_year' => 2021,
    'category' => 'Senior Men',
]);
assertTest($teamDao->get_by_id($teamAId) !== null, 'Team read by ID failed.');
assertTest($teamDao->get_all() !== [], 'Team list returned no rows.');
assertTest(
    $teamDao->update($teamAId, ['category' => 'Test Category']),
    'Team update failed.'
);

$venueId = $venueDao->add([
    'name' => 'DAO Test Venue ' . $suffix,
    'city' => 'Sarajevo',
    'address' => '1 Test Street',
    'capacity' => 1000,
]);
assertTest($venueDao->get_by_id($venueId) !== null, 'Venue read by ID failed.');
assertTest($venueDao->get_all() !== [], 'Venue list returned no rows.');
assertTest(
    $venueDao->update($venueId, ['capacity' => 1100]),
    'Venue update failed.'
);

$playerId = $playerDao->add([
    'first_name' => 'DAO',
    'last_name' => 'Player',
    'birth_date' => '2000-01-01',
    'position' => 'Centre back',
    'jersey_number' => 99,
    'team_id' => $teamAId,
]);
assertTest($playerDao->get_by_id($playerId) !== null, 'Player read by ID failed.');
assertTest($playerDao->get_all() !== [], 'Player list returned no rows.');
assertTest(
    $playerDao->update($playerId, ['position' => 'Right back']),
    'Player update failed.'
);
$joinedPlayers = $playerDao->get_all_with_team();
assertTest(
    count(array_filter(
        $joinedPlayers,
        static fn (array $player): bool => (int) $player['id'] === $playerId
    )) === 1,
    'Player/team JOIN query failed.'
);

$matchId = $matchDao->add([
    'home_team_id' => $teamAId,
    'away_team_id' => $teamBId,
    'venue_id' => $venueId,
    'match_date' => '2026-12-01 18:00:00',
    'home_score' => null,
    'away_score' => null,
    'status' => 'scheduled',
]);
assertTest($matchDao->get_by_id($matchId) !== null, 'Match read by ID failed.');
assertTest($matchDao->get_all() !== [], 'Match list returned no rows.');
assertTest(
    $matchDao->update($matchId, ['status' => 'postponed']),
    'Match update failed.'
);

$standingId = $standingDao->add([
    'team_id' => $teamAId,
    'season' => $season,
    'played' => 1,
    'wins' => 1,
    'draws' => 0,
    'losses' => 0,
    'goals_for' => 30,
    'goals_against' => 20,
    'points' => 2,
    'position' => 1,
]);
assertTest($standingDao->get_by_id($standingId) !== null, 'Standing read by ID failed.');
assertTest($standingDao->get_all() !== [], 'Standing list returned no rows.');
assertTest(
    $standingDao->update($standingId, ['goals_for' => 31]),
    'Standing update failed.'
);
$seasonTotals = $standingDao->get_totals_by_season($season);
assertTest(
    $seasonTotals !== null && (int) $seasonTotals['team_count'] === 1,
    'Standings GROUP BY query failed.'
);

assertTest($matchDao->delete($matchId), 'Match delete failed.');
assertTest($standingDao->delete($standingId), 'Standing delete failed.');
assertTest($playerDao->delete($playerId), 'Player delete failed.');
assertTest($userDao->delete($userId), 'User delete failed.');
assertTest($venueDao->delete($venueId), 'Venue delete failed.');
assertTest($teamDao->delete($teamBId), 'Away team delete failed.');
assertTest($teamDao->delete($teamAId), 'Home team delete failed.');
assertTest($roleDao->delete($roleId), 'Role delete failed.');
$roleDao->rollback();

$roleDao->begin_transaction();
$roleDao->commit();

echo "All DAO smoke tests passed.\n";
