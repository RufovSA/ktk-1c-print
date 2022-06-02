<?php
/**
 * Согласие на обработку персональных данных
 */

/** @var \Twig\Environment $twig */
/** @var array $context */
/** @var string $format */
/** @var string $docId */

/** @var string $uri */

use Dompdf\Dompdf;

$dompdf = new Dompdf();

require_once ROOT_DIR . '/lib/abiturient.php';

$date = explode('.', $context['ДатаРождения']);

function getAge($y, $m, $d)
{
    if ($m > date('m') || $m == date('m') && $d > date('d'))
        return (date('Y') - $y - 1);
    else
        return (date('Y') - $y);
}

$context['Age'] = getAge($date[2], $date[1], $date[0]);

$context['parent'] = $context['СоставСемьи'][0];

$context['parent']['ВидДокументаУдостоверяющегоЛичность'] = httpGetContentById('Catalog_ДокументыУдостоверяющиеЛичность', $context['parent']['ВидДокументаУдостоверяющегоЛичность_Key']);

$context['parent']['ДатаВыдачиДокументаУдостоверяющегоЛичность'] = str_replace('T', ' ', $context['parent']['ДатаВыдачиДокументаУдостоверяющегоЛичность']);
$context['parent']['ДатаВыдачиДокументаУдостоверяющегоЛичность'] = DateTime::createFromFormat('Y-m-d H:i:s', $context['parent']['ДатаВыдачиДокументаУдостоверяющегоЛичность'])->format('Y');

$dompdf->loadHtml($twig->render('priem/approval.twig', $context));

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