<?php
/**
 * Рейниг абитуриентов
 */

/** @var \Twig\Environment $twig */
/** @var array $context */
/** @var string $format */
/** @var string $uri */
/** @var string $docId */

require_once ROOT_DIR . '/lib/priem/companies.php';
require_once ROOT_DIR . '/lib/Optimize.php';

$data = [];
$data['updateTime'] = $context['updateTime'];
unset($context['updateTime']);
$data['companies'] = $context;

$html = $twig->render('priem/rating.twig', $data);

if (!DEBUG) {
    echo Optimize::html($html);
} else {
    echo $html;
}
