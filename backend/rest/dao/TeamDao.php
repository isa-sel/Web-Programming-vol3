<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseDao.php';

final class TeamDao extends BaseDao
{
    protected string $table = 'teams';
    protected array $columns = [
        'id',
        'name',
        'city',
        'founded_year',
        'category',
        'created_at',
    ];
    protected array $writableColumns = ['name', 'city', 'founded_year', 'category'];
    protected array $defaultColumns = [
        'id',
        'name',
        'city',
        'founded_year',
        'category',
        'created_at',
    ];

    public function get_by_name_and_city(string $name, string $city): ?array
    {
        return $this->find_where(['name' => $name, 'city' => $city])[0] ?? null;
    }
}
