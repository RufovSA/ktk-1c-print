<?php
/** Формирование пакета ФИС для импорта Приемных компаний */

/** @var \Twig\Environment $twig */
/** @var Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface $cache */
/** @var array $context */
/** @var string $format */
/** @var string $docId */

require_once ROOT_DIR . '/lib/priem/companies.php';
require_once ROOT_DIR . '/lib/Optimize.php';


header('Content-Type: application/xml; charset=utf-8');

$data = [];
$data['fis_login'] = FIS_LOGIN;
$data['fis_pass'] = FIS_PASS;
$data['companies'] = $context;

$html = $twig->render('priem/fis/campaign.twig', $data);

if (!DEBUG) {
    echo Optimize::html($html);
} else {
    echo $html;
}

