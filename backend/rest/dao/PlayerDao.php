<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseDao.php';

final class PlayerDao extends BaseDao
{
    protected string $table = 'players';
    protected array $columns = [
        'id',
        'first_name',
        'last_name',
        'birth_date',
        'position',
        'jersey_number',
        'team_id',
        'created_at',
    ];
    protected array $writableColumns = [
        'first_name',
        'last_name',
        'birth_date',
        'position',
        'jersey_number',
        'team_id',
    ];
    protected array $defaultColumns = [
        'id',
        'first_name',
        'last_name',
        'birth_date',
        'position',
        'jersey_number',
        'team_id',
        'created_at',
    ];

    public function get_all_with_team(): array
    {
        $sql = <<<'SQL'
            SELECT
                p.id,
                p.first_name,
                p.last_name,
                p.position,
                p.jersey_number,
                p.team_id,
                t.name AS team_name,
                t.city AS team_city
            FROM players AS p
            INNER JOIN teams AS t ON t.id = p.team_id
            ORDER BY p.last_name, p.first_name
            SQL;

        return $this->query($sql)->fetchAll();
    }

    public function get_by_team(int $teamId): array
    {
        return $this->find_where(['team_id' => $teamId], 'jersey_number');
    }

    public function get_by_team_and_jersey(int $teamId, int $jerseyNumber): ?array
    {
        return $this->find_where(['team_id' => $teamId, 'jersey_number' => $jerseyNumber])[0] ?? null;
    }

    public function count_by_team(int $teamId): int
    {
        return $this->count_where(['team_id' => $teamId]);
    }
}
