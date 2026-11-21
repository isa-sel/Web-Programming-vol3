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

    /** Matches where the team plays at home or away, oldest first. */
    public function get_by_team(int $teamId): array
    {
        $sql = sprintf(
            'SELECT %s FROM `matches` WHERE `home_team_id` = :home_team_id OR `away_team_id` = :away_team_id ORDER BY `match_date`',
            $this->quoteColumns($this->defaultColumns)
        );

        return $this->query($sql, ['home_team_id' => $teamId, 'away_team_id' => $teamId])->fetchAll();
    }

    public function count_by_team(int $teamId): int
    {
        return (int) $this->query(
            'SELECT COUNT(*) FROM `matches` WHERE `home_team_id` = :home_team_id OR `away_team_id` = :away_team_id',
            ['home_team_id' => $teamId, 'away_team_id' => $teamId]
        )->fetchColumn();
    }

    public function count_by_venue(int $venueId): int
    {
        return $this->count_where(['venue_id' => $venueId]);
    }
}
