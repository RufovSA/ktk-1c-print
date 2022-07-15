<?php
/**
 * Список абитуриентов
 */

/** @var \Twig\Environment $twig */
/** @var Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface $cache */
/** @var array $context */
/** @var string $format */

header('Content-type: application/json; charset=utf-8');

require_once ROOT_DIR . '/lib/priem/companies.php';

$applicants = [];

foreach ($context as $company) {
    if (isset($company['ПланПриема']) && is_array($company['ПланПриема'])) {
        foreach ($company['ПланПриема'] as $plan) {
            foreach ($plan['Абитуриенты'] as $applicant) {
                $applicants[$applicant['Number']][$applicant['LineNumber']] = $applicant;
                unset(
                    $applicants[$applicant['Number']][$applicant['LineNumber']]['Number'],
                    $applicants[$applicant['Number']][$applicant['LineNumber']]['LineNumber']
                );
            }
        }
    }
}

echo json_encode(array(
    'status' => true,
    'response' => $applicants
));
