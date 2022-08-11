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

$number = 0;

foreach ($context as $company) {
    if (isset($company['ПланПриема']) && is_array($company['ПланПриема'])) {
        foreach ($company['ПланПриема'] as $plan) {
            $place = 1;
            foreach ($plan['Абитуриенты'] as $applicant) {
                $applicants[$number]['number'] = $applicant['Number'];
                $applicants[$number]['lineNumber'] = $applicant['LineNumber'];
                $applicants[$number]['place'] = $place;
                $applicants[$number]['mark'] = $applicant['СреднийБаллАттестата'];
                $applicants[$number]['isCopy'] = $applicant['КопияАттестата'] != '1' ? false: true;
                $applicants[$number]['education'] = $plan['ПрограммаОбучения']['БазовоеОбразование'] == 'ОсновноеОбщее' ? 0: 1;
                $applicants[$number]['specialization'] = $plan['ПрограммаОбучения']['Description'];
                $applicants[$number]['qualification'] = $plan['ПрограммаОбучения']['Квалификации'][0]['Description'];
                if ($applicant['КопияАттестата'] != '1') $place++;
                $number++;
            }
        }
    }
}

echo json_encode(array(
    'status' => true,
    'updateTime' => $context['updateTime'],
    'response' => $applicants
));
