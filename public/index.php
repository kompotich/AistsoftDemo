<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/autoload.php';

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$documentationFiles = [
    '/swagger' => ['swagger.html', 'text/html; charset=utf-8'],
    '/swagger/' => ['swagger.html', 'text/html; charset=utf-8'],
    '/swagger.html' => ['swagger.html', 'text/html; charset=utf-8'],
    '/openapi/checko.json' => ['openapi/checko.json', 'application/json; charset=utf-8'],
    '/openapi/app.json' => ['openapi/app.json', 'application/json; charset=utf-8'],
];

if (is_string($requestPath) && isset($documentationFiles[$requestPath])) {
    [$file, $contentType] = $documentationFiles[$requestPath];
    header('Content-Type: ' . $contentType);
    readfile(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file));
    return;
}

$projectRoot = dirname(__DIR__);
$configJson = file_get_contents($projectRoot . DIRECTORY_SEPARATOR . 'config.json');
if ($configJson === false) {
    throw new RuntimeException('Unable to read config.json.');
}

$config = json_decode($configJson, true, 512, JSON_THROW_ON_ERROR);
$environment = parse_ini_file($projectRoot . DIRECTORY_SEPARATOR . '.env');
if ($environment === false || empty($environment['CHECKO_API_KEY'])) {
    throw new RuntimeException('CHECKO_API_KEY is missing from the .env file.');
}

$config['key'] = $environment['CHECKO_API_KEY'];
$config['database_path'] = $projectRoot . DIRECTORY_SEPARATOR
    . str_replace('/', DIRECTORY_SEPARATOR, $config['database_path']);

if (is_string($requestPath) && preg_match('~^/app(?:/|$)~', $requestPath) === 1) {
    $database = new \AistsoftDemo\Database\SqliteDatabase(
        $config['database_path'],
        \AistsoftDemo\Database\AistsoftDemoRepository::entityClasses()
    );
    $repository = new \AistsoftDemo\Database\AistsoftDemoRepository($database);
    $appApi = new \AistsoftDemo\Http\AppApi($repository);

    $appApi->run();
} else {
    $checkoClient = new \AistsoftDemo\Client\CheckoClient($config);
    $proxyCheckoApi = new \AistsoftDemo\Http\ProxyCheckoApi($checkoClient);

    $proxyCheckoApi->run();
}
