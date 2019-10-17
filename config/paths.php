<?php
/**
 * Path configuration constants
 */
if (! defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', dirname(__DIR__));
define('APP', ROOT . DS . 'src');
define('CONFIG', ROOT . DS . 'config');
define('DATABASE', ROOT . DS . 'database');
define('PLUGINS', ROOT . DS . 'plugins');
define('TESTS', ROOT . DS . 'tests');
define('WEBROOT', ROOT . DS . 'public');
define('TMP', ROOT . DS . 'tmp');
define('LOGS', ROOT . DS . 'logs');
define('CACHE', TMP . DS . 'cache');
define('ORIGIN', ROOT . DS . 'vendor'. DS . 'originphp'. DS . 'framework');
