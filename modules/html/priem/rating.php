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

$data = [];
$data['companies'] = $context;

echo $twig->render('priem/rating.twig', $data);
