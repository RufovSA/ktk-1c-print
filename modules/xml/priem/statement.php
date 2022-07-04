<?php
/** Формирование пакета ФИС для импорта Заявлений */

/** @var \Twig\Environment $twig */
/** @var Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface $cache */
/** @var array $context */
/** @var string $format */
/** @var string $docId */

$is_import = 'import';

require_once ROOT_DIR . '/lib/priem/companies.php';
require_once ROOT_DIR . '/lib/Optimize.php';

$data = [];
$data['fis_login'] = FIS_LOGIN;
$data['fis_pass'] = FIS_PASS;
$data['companies'] = $context;

dump($data);

header('Content-Type: application/xml; charset=utf-8');
$html = $twig->render('priem/fis/statement.twig', $data);

if (!DEBUG) {
    echo Optimize::html($html);
} else {
    echo $html;
}

