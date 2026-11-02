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
}
