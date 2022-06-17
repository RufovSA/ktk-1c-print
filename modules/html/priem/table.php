<?php
/**
 * Таблица цифр приема
 */

/** @var \Twig\Environment $twig */
/** @var array $context */
/** @var string $format */
/** @var string $uri */
/** @var string $docId */

require_once ROOT_DIR . '/lib/priem/companies.php';
require_once ROOT_DIR . '/lib/Optimize.php';

$data = [];
$data['companies'] = $context;

$html = $twig->render('priem/table.twig', $data);

if (!DEBUG) {
    echo Optimize::html($html);
} else {
    echo $html;
}
