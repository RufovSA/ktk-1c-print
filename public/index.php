<?php

define('ROOT_DIR', dirname(__DIR__));

require_once ROOT_DIR . '/vendor/autoload.php';

if (!is_file(ROOT_DIR . '/config.php')) {
    echo '<p style="color: red">Конфигурационный файл (config.php) не найден.</p>';
    exit;
}

require_once ROOT_DIR . '/config.php';

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();

$uri = $_GET['q'] ?? '';
$format = substr($uri, strrpos($uri, '.') + 1);
$uri = substr($uri, 0, strrpos($uri, '.'));

$client = new Kily\Tools1C\OData\Client(HOST_1C . PATH_1C . '/odata/standard.odata/',[
    'auth' => [
        USER_1C,
        PASS_1C
    ],
    'timeout' => 300,
]);

/**
 * Получиние данных от 1С
 *
 * @param string $obj
 * @param string $filter
 * @return array
 */
function httpGetContent(string $obj, string $filter = ''): array
{
    global $client;
    return $client->{$obj}->get($filter)->values();
}

/**
 * Получиние записи от 1С по идентификатору
 *
 * @param string $obj
 * @param string $id
 * @return array
 */
function httpGetContentById(string $obj, string $id): array
{
    global $client;
    try {
        return $client->{$obj}->get($id)->first();
    } catch (Exception $e) {
        require_once ROOT_DIR . '/modules/404.php';
        exit();
    }
}

/**
 * Отобразить данные
 *
 * @param mixed $data Данные
 * @return void
 */
function dump($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    exit;
}

$docId = !empty($_REQUEST['doc_id']) ? $_REQUEST['doc_id']: null;
if (is_null($docId)) {
    require_once ROOT_DIR . '/modules/404.php';
    exit;
}

$loader = new \Twig\Loader\FilesystemLoader(ROOT_DIR . '/views/');
$twig = new \Twig\Environment($loader, [
    'cache' => ROOT_DIR . '/cache',
    'debug' => true
]);

if (is_file(ROOT_DIR . '/modules/' . $format  . '/' . $uri . '.php')) {
    require_once ROOT_DIR . '/modules/' . $format . '/' . $uri . '.php';
} else {
    require_once ROOT_DIR . '/modules/404.php';
}