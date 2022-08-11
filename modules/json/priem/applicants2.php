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
            $place = 1;
            foreach ($plan['Абитуриенты'] as $applicant) {
                $applicants[$applicant['Number']][] = [
                    'number' => $applicant['Number'],
                    'lineNumber' => $applicant['LineNumber'],
                    'place' => $place,
                    'mark' => $applicant['СреднийБаллАттестата'],
                    'isCopy' => $applicant['КопияАттестата'] != '1' ? false: true,
                    'education' => $plan['ПрограммаОбучения']['БазовоеОбразование'] == 'ОсновноеОбщее' ? 0: 1,
                    'specialization' => $plan['ПрограммаОбучения']['Description'],
                    'qualification' => $plan['ПрограммаОбучения']['Квалификации'][0]['Description'],
                ];
                if ($applicant['КопияАттестата'] != '1') $place++;
            }
        }
    }
}

echo json_encode(array(
    'status' => true,
    'updateTime' => $context['updateTime'],
    'response' => $applicants
));
