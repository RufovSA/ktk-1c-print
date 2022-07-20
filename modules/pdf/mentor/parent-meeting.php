<?php
/**
 * Протокол родительского собрания
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

$context = httpGetContent('Document_РодительскоеСобрание', "Number eq '{$docId}'");
$context = $context[0] ?? [];
$context = (array) $context;
if (!$context) {
    require_once ROOT_DIR . '/modules/404.php';
    exit;
}

$context['Date'] = str_replace('T', ' ', $context['Date']);
$context['Date'] = DateTime::createFromFormat('Y-m-d H:i:s', $context['Date'])->format('d.m.Y H:i:s');

$context['ПланируемаяДатаПроведения'] = str_replace('T', ' ', $context['ПланируемаяДатаПроведения']);
$context['ПланируемаяДатаПроведения'] = DateTime::createFromFormat('Y-m-d H:i:s', $context['ПланируемаяДатаПроведения'])->format('d.m.Y');

$context['ФактическаяДатаПроведения'] = str_replace('T', ' ', $context['ФактическаяДатаПроведения']);
$context['ФактическаяДатаПроведения'] = DateTime::createFromFormat('Y-m-d H:i:s', $context['ФактическаяДатаПроведения'])->format('d.m.Y');

$context['УчебнаяГруппа'] = httpGetContentById('Catalog_УчебныеГруппы', $context['УчебнаяГруппа_Key']);
$context['Куратор'] = httpGetContentById('Catalog_Сотрудники', $context['Куратор_Key']);

$context['attended'] = 0;

for ($i = 0; $i < count($context['ИндивидуальныеБеседы']); $i++) {
    $context['ИндивидуальныеБеседы'][$i]['student'] = httpGetContentById('Catalog_Студенты', $context['ИндивидуальныеБеседы'][$i]['СРодителямиСтудента_Key']);
    if ($context['ИндивидуальныеБеседы'][$i]['БеседаСостоялась']) $context['attended']++;
}

$dompdf = new Dompdf();

$dompdf->loadHtml($twig->render('mentor/parent-meeting.twig', $context));

$options = $dompdf->getOptions();

$options->set('enable_remote', true);
//$options->set('defaultFont', 'dejavu sans');

$dompdf->setOptions($options);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('statement-' . $docId . '.pdf', ['Attachment' => 0]);
