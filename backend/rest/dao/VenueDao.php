<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseDao.php';

final class VenueDao extends BaseDao
{
    protected string $table = 'venues';
    protected array $columns = ['id', 'name', 'city', 'address', 'capacity', 'created_at'];
    protected array $writableColumns = ['name', 'city', 'address', 'capacity'];
    protected array $defaultColumns = ['id', 'name', 'city', 'address', 'capacity', 'created_at'];
}
