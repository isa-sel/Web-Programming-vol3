<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseDao.php';

final class StandingDao extends BaseDao
{
    protected string $table = 'standings';
    protected array $columns = [
        'id',
        'team_id',
        'season',
        'played',
        'wins',
        'draws',
        'losses',
        'goals_for',
        'goals_against',
        'points',
        'position',
        'created_at',
    ];
    protected array $writableColumns = [
        'team_id',
        'season',
        'played',
        'wins',
        'draws',
        'losses',
        'goals_for',
        'goals_against',
        'points',
        'position',
    ];
    protected array $defaultColumns = [
        'id',
        'team_id',
        'season',
        'played',
        'wins',
        'draws',
        'losses',
        'goals_for',
        'goals_against',
        'points',
        'position',
        'created_at',
    ];

    public function get_totals_by_season(string $season): ?array
    {
        $sql = <<<'SQL'
            SELECT
                season,
                COUNT(*) AS team_count,
                SUM(played) AS played,
                SUM(wins) AS wins,
                SUM(draws) AS draws,
                SUM(losses) AS losses,
                SUM(goals_for) AS goals_for,
                SUM(goals_against) AS goals_against,
                SUM(points) AS points
            FROM standings
            WHERE season = :season
            GROUP BY season
            SQL;

        return $this->query_unique($sql, ['season' => $season]);
    }
}
