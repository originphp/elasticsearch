<?php

/**
 * This is the bootstrap for plugin when using as standalone (for development). Do not
 * use this bootstrap as a plugin. .gitattributes has blocked this from being installed.
 */
use Origin\Core\Config;

require __DIR__ . '/paths.php';
require ORIGIN . '/src/bootstrap.php';

Config::write('App.namespace', 'Elasticsearch');

use Origin\Model\ConnectionManager;
use Elasticsearch\Elasticsearch;
use Origin\Log\Log;

Log::config('default', [
    'engine' => 'File',
    'file' => LOGS . '/application.log'
]);

ConnectionManager::config('test', [
    'host' => env('DB_HOST', '127.0.0.1'),
    'database' => 'elasticsearch',
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'engine' => env('DB_ENGINE', 'mysql')
]);


Elasticsearch::config('test', [
    'host' => env('ELASTICSEARCH_HOST', '127.0.0.1'),
    'port' => 9200,
    'ssl' => false,
    'timeout' => 400,
]);
