<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseDao.php';

final class RoleDao extends BaseDao
{
    protected string $table = 'roles';
    protected array $columns = ['id', 'name', 'description', 'created_at'];
    protected array $writableColumns = ['name', 'description'];
    protected array $defaultColumns = ['id', 'name', 'description', 'created_at'];
}
