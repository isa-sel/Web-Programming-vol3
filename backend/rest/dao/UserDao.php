<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseDao.php';

final class UserDao extends BaseDao
{
    protected string $table = 'users';
    protected array $columns = [
        'id',
        'first_name',
        'last_name',
        'email',
        'password_hash',
        'role_id',
        'is_active',
        'created_at',
    ];
    protected array $writableColumns = [
        'first_name',
        'last_name',
        'email',
        'password_hash',
        'role_id',
        'is_active',
    ];
    protected array $defaultColumns = [
        'id',
        'first_name',
        'last_name',
        'email',
        'role_id',
        'is_active',
        'created_at',
    ];

    public function get_all(?array $columns = null): array
    {
        if ($columns !== null && in_array('password_hash', $columns, true)) {
            throw new InvalidArgumentException('User lists must not include password hashes.');
        }

        return parent::get_all($columns);
    }

    public function get_by_email(string $email): ?array
    {
        $sql = sprintf(
            'SELECT %s FROM `%s` WHERE `email` = :email',
            $this->quoteColumns($this->columns),
            $this->table
        );

        return $this->query_unique($sql, ['email' => $email]);
    }
}
