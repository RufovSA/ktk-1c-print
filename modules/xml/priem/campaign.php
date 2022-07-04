<?php
/** Формирование пакета ФИС для импорта Приемных компаний */

/** @var \Twig\Environment $twig */
/** @var Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface $cache */
/** @var array $context */
/** @var string $format */
/** @var string $docId */

require_once ROOT_DIR . '/lib/Optimize.php';

$docId = !empty($_REQUEST['doc_id']) ? $_REQUEST['doc_id'] : null;
if (is_null($docId)) {
    require_once ROOT_DIR . '/modules/404.php';
    exit;
}

header('Content-Type: application/xml; charset=utf-8');

//$data = httpGetContentById('Catalog_ПриемныеКампании', $_REQUEST['doc_id']);
$data = httpGetContent('Catalog_ПриемныеКампании', "Code eq '{$_REQUEST['doc_id']}'");
$data = $data[0];

unset(
    $data['Организация@navigationLinkUrl'],
    $data['Predefined'],
    $data['ИндивидуальныеДостижения'],
    $data['ЕстьЦелевыеНаправления'],
    $data['IsFolder'],
    $data['Parent_Key'],
    $data['DeletionMark'],
    $data['DataVersion'],
    $data['Организация_Key'],
    $data['PredefinedDataName']
);

$admission = httpGetContent('Document_СправочникиФИС', "ПриемнаяКампания_Key eq guid'{$data['Ref_Key']}' and КодСправочника eq '10'");
$data['admission'] = [];
$i = 0;
foreach ($admission[0]['ДанныеСправочников'] as $item) {
    $spesial = httpGetContentById('Catalog_Специальности', $item['Соответствие']);
    $plan = httpGetContent('Document_ПланПриема', "ПриемнаяКампания_Key eq guid'{$data['Ref_Key']}' and Специальность_Key eq guid'{$spesial['Ref_Key']}'");
    // ПрограммаОбучения_Key
    $data['admission'][$i]['id'] = $spesial['Ref_Key'];
    $data['admission'][$i]['КодЭлемента'] = $item['КодЭлемента'];
    $data['admission'][$i]['ПрефиксУчебныхГрупп'] = $spesial['ПрефиксУчебныхГрупп'];
    $data['admission'][$i]['ФормаОбучения'] = 1;
    $data['admission'][$i]['Финансирование'] = 1;
    $data['admission'][$i]['План'] = 0;
    foreach ($plan as $var) {
        $data['admission'][$i]['План'] += $var['Данные'][0]['План'];
    }
    $i++;
}

$data['fis_login'] = FIS_LOGIN;
$data['fis_pass'] = FIS_PASS;

//dump($data);

$html = $twig->render('priem/fis/campaign.twig', $data);

if (!DEBUG) {
    echo Optimize::html($html);
} else {
    echo $html;
}

