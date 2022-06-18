<?php
/**
 * Рабочий учебный план
 */

/** @var \Twig\Environment $twig */
/** @var array $context */
/** @var string $format */
/** @var string $docId */
/** @var string $uri */

use Dompdf\Dompdf;
use RedBeanPHP\R;

$docId = !empty($_REQUEST['doc_id']) ? $_REQUEST['doc_id'] : null;
if (is_null($docId)) {
    require_once ROOT_DIR . '/modules/404.php';
    exit;
}

$context = httpGetContent('Catalog_РабочиеУчебныеПланы', "Code eq '{$docId}'");
$context = $context[0] ?? [];
$context = (array) $context;

if (isset($context['IsFolder']) && $context['IsFolder']) {
    require_once ROOT_DIR . '/modules/404.php';
    exit;
}

$context['company_name'] = COMPANY_NAME;
$context['company_name_brief'] = COMPANY_NAME_BRIEF;
$context['company_director'] = COMPANY_DIRECTOR;
$context['format'] = 'pdf';

$context['ДатаУтверждения'] = (new DateTime($context['ДатаУтверждения']))->format('d.m.Y');

if ($context['Специальность_Key'] != '00000000-0000-0000-0000-000000000000')
    $context['Специальность'] = httpGetContentById('Catalog_Специальности', $context['Специальность_Key']);

if ($context['Квалификация_Key'] != '00000000-0000-0000-0000-000000000000')
    $context['Квалификация'] = httpGetContentById('Catalog_Квалификации', $context['Квалификация_Key']);

$context['ГодКонцаПодготовки'] = $context['ГодНачалаПодготовки'] + $context['НормативныйСрокОбученияЛет'];
if ($context['НормативныйСрокОбученияМесяцев'] == '10') $context['ГодКонцаПодготовки']++;

$context['ЧислоСеместров'] = $context['НормативныйСрокОбученияЛет'];
if ($context['НормативныйСрокОбученияМесяцев'] == '10') $context['ЧислоСеместров']++;
$context['ЧислоСеместров'] = (int) $context['ЧислоСеместров'];

$context['СтрокиРУП'] = httpGetContent('Catalog_СтрокиРУП', "Owner_Key eq guid'{$context['Ref_Key']}'", [
    'query' => [
        '$orderby' => 'ПриоритетВывода asc'
    ]
]);

$_semesters = httpGetContent('Catalog_КурсыСеместры');
$semesters = [];
foreach ($_semesters as $item) {
    $semesters[$item['Ref_Key']] = $item;
}
unset($_semesters, $item);

for ($i = 0; $i < count($context['СтрокиРУП']); $i++) {
    $data = $context['СтрокиРУП'][$i]['РаспределениеПоКурсамИСеместрам'];
    $context['СтрокиРУП'][$i]['РаспределениеПоКурсамИСеместрам'] = [];
    for ($j = 0; $j < count($data); $j++) {
        $key = $data[$j]['Семестр_Key'];
        $data[$j]['Семестр'] = $semesters[$key] ?? null;
        if (isset($data[$j]['Семестр']['Description'])) {
            switch ($data[$j]['ФормаКонтроля']) {
                case 'Зачет':
                    $data[$j]['ФормаКонтроля'] = 'З';
                    break;
                case 'Экзамен':
                    $data[$j]['ФормаКонтроля'] = 'Э';
                    break;
                case 'КонтрольнаяРабота':
                    $data[$j]['ФормаКонтроля'] = 'КР';
                    break;
                case 'ДифференцированныйЗачет':
                    $data[$j]['ФормаКонтроля'] = 'ДЗ';
                    break;
                case 'КурсовойПроект':
                    $data[$j]['ФормаКонтроля'] = 'КП';
                    break;
                case 'Тестирование':
                    $data[$j]['ФормаКонтроля'] = 'Т';
                    break;
                case 'Собеседование':
                    $data[$j]['ФормаКонтроля'] = 'С';
                    break;
                case 'ПрактическиеРаботы':
                    $data[$j]['ФормаКонтроля'] = 'ПР';
                    break;
                case 'ДругиеФормыКонтроля':
                    $data[$j]['ФормаКонтроля'] = 'ДФР';
                    break;
                case 'КвалификационныйЭкзамен':
                    $data[$j]['ФормаКонтроля'] = 'КЭ';
                    break;
                case 'ДемонстрационныйЭкзамен':
                    $data[$j]['ФормаКонтроля'] = 'ДЭ';
                    break;
            }
            $code = substr($data[$j]['Семестр']['Description'], 0, 1);
            unset($data[$j]['Семестр']);
            $context['СтрокиРУП'][$i]['РаспределениеПоКурсамИСеместрам'][$code] = $data[$j];
        }
    }
}

//dump($context['СтрокиРУП']);
$dompdf = new Dompdf();
$dompdf->loadHtml($twig->render('training-part/curriculum.twig', $context));

$options = $dompdf->getOptions();

$options->set('enable_remote', true);
//$options->set('defaultFont', 'dejavu sans');

$dompdf->setOptions($options);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('curriculum-' . $docId . '.pdf', ['Attachment' => 0]);
