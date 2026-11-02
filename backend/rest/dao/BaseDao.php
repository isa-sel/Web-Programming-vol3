<?php

declare(strict_types=1);

abstract class BaseDao
{
    protected string $table;
    protected array $columns;
    protected array $writableColumns;
    protected array $defaultColumns;

    private static ?PDO $connection = null;

    public function __construct()
    {
        if ($this->table === '' || $this->columns === [] || $this->writableColumns === []) {
            throw new LogicException('DAO table and column definitions must not be empty.');
        }
    }

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $configPath = dirname(__DIR__, 2) . '/config/config.php';
        if (!is_file($configPath)) {
            $configPath = dirname(__DIR__, 2) . '/config/config.example.php';
        }
        require_once $configPath;

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            Config::DB_HOST(),
            Config::DB_PORT(),
            Config::DB_NAME()
        );

        self::$connection = new PDO(
            $dsn,
            Config::DB_USER(),
            Config::DB_PASSWORD(),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return self::$connection;
    }

    protected function query(string $sql, array $parameters = []): PDOStatement
    {
        $statement = self::getConnection()->prepare($sql);
        $statement->execute($parameters);

        return $statement;
    }

    protected function query_unique(string $sql, array $parameters = []): ?array
    {
        $row = $this->query($sql, $parameters)->fetch();

        return $row === false ? null : $row;
    }

    protected function execute(string $sql, array $parameters = []): int
    {
        return $this->query($sql, $parameters)->rowCount();
    }

    public function only_columns(array $columns, ?array $allowedColumns = null): array
    {
        $allowedColumns ??= $this->columns;
        $allowed = array_fill_keys($allowedColumns, true);

        foreach ($columns as $column) {
            if (!is_string($column) || !isset($allowed[$column])) {
                throw new InvalidArgumentException('Unknown or non-writable column requested.');
            }
        }

        if ($columns === []) {
            throw new InvalidArgumentException('At least one column must be selected.');
        }

        return array_values(array_unique($columns));
    }

    public function get_all(?array $columns = null): array
    {
        $columns = $this->only_columns($columns ?? $this->defaultColumns);
        $sql = sprintf(
            'SELECT %s FROM `%s`',
            $this->quoteColumns($columns),
            $this->table
        );

        return $this->query($sql)->fetchAll();
    }

    public function get_by_id(int $id, ?array $columns = null): ?array
    {
        $columns = $this->only_columns($columns ?? $this->defaultColumns);
        $sql = sprintf(
            'SELECT %s FROM `%s` WHERE `id` = :id',
            $this->quoteColumns($columns),
            $this->table
        );

        return $this->query_unique($sql, ['id' => $id]);
    }

    public function add(array $data): int
    {
        $columns = $this->only_columns(array_keys($data), $this->writableColumns);
        $placeholders = array_map(
            static fn (string $column): string => ':value_' . $column,
            $columns
        );
        $parameters = [];
        foreach ($columns as $column) {
            $parameters['value_' . $column] = $data[$column];
        }

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $this->table,
            $this->quoteColumns($columns),
            implode(', ', $placeholders)
        );
        $this->execute($sql, $parameters);

        return (int) self::getConnection()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $columns = $this->only_columns(array_keys($data), $this->writableColumns);
        $assignments = [];
        $parameters = ['id' => $id];

        foreach ($columns as $column) {
            $placeholder = 'value_' . $column;
            $assignments[] = sprintf('`%s` = :%s', $column, $placeholder);
            $parameters[$placeholder] = $data[$column];
        }

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE `id` = :id',
            $this->table,
            implode(', ', $assignments)
        );

        return $this->execute($sql, $parameters) > 0;
    }

    public function delete(int $id): bool
    {
        $sql = sprintf('DELETE FROM `%s` WHERE `id` = :id', $this->table);

        return $this->execute($sql, ['id' => $id]) > 0;
    }

    public function begin_transaction(): void
    {
        self::getConnection()->beginTransaction();
    }

    public function commit(): void
    {
        self::getConnection()->commit();
    }

    public function rollback(): void
    {
        self::getConnection()->rollBack();
    }

    protected function quoteColumns(array $columns): string
    {
        return implode(', ', array_map(
            static fn (string $column): string => sprintf('`%s`', $column),
            $columns
        ));
    }
}
