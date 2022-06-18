<?php
/**
 * Получиние данных от 1С
 *
 * @param string $obj
 * @param string $filter
 * @return array
 */
function httpGetContent(string $obj, string $filter = '', $options = []): array
{
    global $client;
    return $client->{$obj}->get(null, $filter, $options)->values();
}

/**
 * Получиние записи от 1С по идентификатору
 *
 * @param string $obj
 * @param null|string $id
 * @return array
 */
function httpGetContentById(string $obj, ?string $id = null): array
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