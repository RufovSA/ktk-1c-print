<?php
/**
 * Заявление абитуриента
 */

/** @var \Twig\Environment $twig */
/** @var array $context */
/** @var string $format */
/** @var string $docId */
/** @var string $uri */

use Dompdf\Dompdf;

$dompdf = new Dompdf();

require_once ROOT_DIR . '/lib/abiturient.php';

$dompdf->loadHtml($twig->render('priem/statement.twig', $context));

$options = $dompdf->getOptions();

$options->set('enable_remote', true);
//$options->set('defaultFont', 'dejavu sans');

$dompdf->setOptions($options);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('statement.pdf', ['Attachment' => 0]);