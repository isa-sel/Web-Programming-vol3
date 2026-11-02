<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseDao.php';

function getDatabaseConnection(): PDO
{
    return BaseDao::getConnection();
}
