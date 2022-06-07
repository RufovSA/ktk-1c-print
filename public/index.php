<?php

define('ROOT_DIR', dirname(__DIR__));

require_once ROOT_DIR . '/vendor/autoload.php';
require_once ROOT_DIR . '/lib/init.php';

/** @var string $format */
/** @var string $uri */

if (is_file(ROOT_DIR . '/modules/' . $format  . '/' . $uri . '.php')) {
    require_once ROOT_DIR . '/modules/' . $format . '/' . $uri . '.php';
} else {
    require_once ROOT_DIR . '/modules/404.php';
}