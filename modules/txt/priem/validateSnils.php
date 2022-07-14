<?php
if (!isset($_REQUEST['snils'])) {
    require_once ROOT_DIR . '/modules/404.php';
    exit();
}

require_once ROOT_DIR . '/lib/DataValidation.php';

$snils = str_replace(' ', '', $_REQUEST['snils']);
$snils = str_replace('-', '', $snils);

DataValidation::validateSnils($snils);

echo DataValidation::$message;