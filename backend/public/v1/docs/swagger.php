<?php

/*
 * Generates the OpenAPI document (JSON) from the attributes in doc_setup.php and paths/.
 * Open it directly to check that the document can be generated:
 *     http://localhost/Web-Programming-vol-3/backend/public/v1/docs/swagger.php
 */

// One PHP warning in the middle of the JSON would break Swagger UI
ini_set('display_errors', '0');
error_reporting(E_ALL);

require __DIR__ . '/../../../vendor/autoload.php';

/*
 * The API base URL is computed from this request (".../backend/public/v1/docs/swagger.php"
 * -> ".../backend/"), so it is right on XAMPP, with php -S and on the production server.
 */
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$backendPath = dirname($_SERVER['SCRIPT_NAME'] ?? '/backend/public/v1/docs/swagger.php', 4);
define('API_BASE_URL', $scheme . '://' . $host . rtrim(str_replace('\\', '/', $backendPath), '/') . '/');

// swagger-php reads attributes with reflection, which only works on loaded classes
$sources = array_merge([__DIR__ . '/doc_setup.php'], glob(__DIR__ . '/paths/*.php'));
foreach ($sources as $file) {
    require_once $file;
}

$openapi = (new \OpenApi\Generator())->generate($sources);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
echo $openapi->toJson();
