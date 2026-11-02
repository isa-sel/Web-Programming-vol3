<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseDao.php';

final class MatchDao extends BaseDao
{
    protected string $table = 'matches';
    protected array $columns = [
        'id',
        'home_team_id',
        'away_team_id',
        'venue_id',
        'match_date',
        'home_score',
        'away_score',
        'status',
        'created_at',
    ];
    protected array $writableColumns = [
        'home_team_id',
        'away_team_id',
        'venue_id',
        'match_date',
        'home_score',
        'away_score',
        'status',
    ];
    protected array $defaultColumns = [
        'id',
        'home_team_id',
        'away_team_id',
        'venue_id',
        'match_date',
        'home_score',
        'away_score',
        'status',
        'created_at',
    ];
}
