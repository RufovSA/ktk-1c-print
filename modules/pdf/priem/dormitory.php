<?php
/**
 * Заявление на общажитие
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

$context['fio_p'] = $nc->q($context['Фамилия'] . ' ' . $context['Имя'] . ' ' . $context['Отчество'], \NCL::$RODITLN);
$context['fio_d'] = $nc->q($context['Фамилия'] . ' ' . $context['Имя'] . ' ' . $context['Отчество'], \NCL::$DATELN);

$context['data_start'] = date('Y');
$context['data_end'] = date('Y') + 1;

$context['parent'] = $context['СоставСемьи'][0];

$dompdf->loadHtml($twig->render('priem/dormitory.twig', $context));

$options = $dompdf->getOptions();

$options->set('enable_remote', true);
//$options->set('defaultFont', 'dejavu sans');

$dompdf->setOptions($options);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('dormitory-' . $docId . '.pdf', ['Attachment' => 0]);