<?php
/**
 * Список приёмных компаний
 */

/** @var \Twig\Environment $twig */
/** @var Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface $cache */
/** @var array $context */
/** @var string $format */
/** @var string $docId */

header('Content-type: application/json; charset=utf-8');

require_once ROOT_DIR . '/lib/priem/companies.php';

echo json_encode(array(
    'status' => true,
    'response' => $context
));
