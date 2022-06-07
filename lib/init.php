<?php
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\CacheManager;

if (!is_file(ROOT_DIR . '/config.php')) {
    echo '<p style="color: red">Конфигурационный файл (config.php) не найден.</p>';
    exit;
}

require_once ROOT_DIR . '/config.php';

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();

$uri = $_GET['q'] ?? '';

define('HOME_URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])));

$format = substr($uri, strrpos($uri, '.') + 1);
$uri = substr($uri, 0, strrpos($uri, '.'));

$client = new Kily\Tools1C\OData\Client(HOST_1C . PATH_1C . '/odata/standard.odata/',[
    'auth' => [
        USER_1C,
        PASS_1C
    ],
    'timeout' => 300,
]);

require_once ROOT_DIR . '/lib/functions.php';

$loader = new \Twig\Loader\FilesystemLoader(ROOT_DIR . '/views/');
$twig = new \Twig\Environment($loader, [
    'cache' => ROOT_DIR . '/cache',
    'debug' => true
]);

if (TYPE_CACHE == 'redis' || TYPE_CACHE == 'memcache') {
    CacheManager::setDefaultConfig(new ConfigurationOption([
        'host' => CACHE_HOST,
        'port' => CACHE_POST,
        'path' => ROOT_DIR . '/cache/'
    ]));
} else {
    CacheManager::setDefaultConfig(new ConfigurationOption([
        'path' => ROOT_DIR . '/cache/'
    ]));
}

$cache = CacheManager::getInstance( TYPE_CACHE );
