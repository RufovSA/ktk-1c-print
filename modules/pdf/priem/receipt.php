<?php
/**
 * Расписка абитуриента
 */

/** @var \Twig\Environment $twig */
/** @var array $context */
/** @var string $format */
/** @var string $docId */

/** @var string $uri */

use Dompdf\Dompdf;

$docId = !empty($_REQUEST['doc_id']) ? $_REQUEST['doc_id'] : null;
if (is_null($docId)) {
    require_once ROOT_DIR . '/modules/404.php';
    exit;
}

$dompdf = new Dompdf();

require_once ROOT_DIR . '/lib/abiturient.php';
require_once ROOT_DIR . '/lib/ncl/NCLNameCaseRu.php';

$nc = new \NCLNameCaseRu();

$context['ВидДокументаОбразованияКласс'] = '(11 кл.)';
if ($context['ВидДокументаОбразования'] == 'АттестатОсновноеОбщее') $context['ВидДокументаОбразованияКласс'] = '(9 кл.)';

$context['fio_r'] = $nc->q($context['Фамилия'] . ' ' . $context['Имя'] . ' ' . $context['Отчество'], \NCL::$RODITLN);
$context['ПодачаЗаявлений'] = $context['ПодачаЗаявлений'][0];

for ($i = 0; $i < count($context['ДокументыДляПоступления']); $i++) {
    $context['ДокументыДляПоступления'][$i]['ДокументДляПоступления'] = httpGetContentById('Catalog_ДокументыДляПоступления', $context['ДокументыДляПоступления'][$i]['ДокументДляПоступления_Key']);
}

$context['count_receipt'] = $_REQUEST['count'] ?? 1;

$dompdf->loadHtml($twig->render('priem/receipt.twig', $context));

$options = $dompdf->getOptions();

$options->set('enable_remote', true);
//$options->set('defaultFont', 'dejavu sans');

$dompdf->setOptions($options);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('receipt-' . $docId . '.pdf', ['Attachment' => 0]);