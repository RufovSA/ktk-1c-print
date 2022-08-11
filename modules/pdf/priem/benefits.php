<?php
/**
 * Отчет по льготам
 */

/** @var \Twig\Environment $twig */
/** @var array $context */
/** @var string $format */
/** @var string $docId */

/** @var string $uri */

use Dompdf\Dompdf;

$company = $_REQUEST['company'] ?? null; // 1faa40f7-955f-11ec-80c3-000c29334945
$benefit = $_REQUEST['benefit'] ?? null; // 557a7507-88c2-11ec-80c2-000c29334945

if (is_null($company) || is_null($benefit)) {
	require_once ROOT_DIR . '/modules/404.php';
	exit;
}

$context['benefit'] = httpGetContentById('Catalog_Льготы', $benefit);
$context['statements'] = httpGetContent('Document_АнкетаАбитуриента', "ПриемнаяКампания_Key eq guid'{$company}' and Льгота_Key eq guid'{$benefit}'");
//dump($context);
for ($i = 0; $i < count($context['statements']); $i++) {
	$context['statements'][$i]['ПодачаЗаявлений'][0]['ПрограммаОбучения'] = httpGetContentById('Catalog_ПрограммыСПО', $context['statements'][$i]['ПодачаЗаявлений'][0]['ПрограммаОбучения_Key']);

	$context['statements'][$i]['КопияАттестата'] = '';
	for ($j = 0; $j < count($context['statements'][$i]['ДокументыДляПоступления']); $j++) {
		if ($context['statements'][$i]['ДокументыДляПоступления'][$j]['ДокументДляПоступления_Key'] != '00000000-0000-0000-0000-000000000000') {
			$data = httpGetContentById('Catalog_ДокументыДляПоступления', $context['statements'][$i]['ДокументыДляПоступления'][$j]['ДокументДляПоступления_Key']);
			if ($data['ТипДокумента'] == 'ДокументОбОбразовании') {
				$context['statements'][$i]['КопияАттестата'] = $context['statements'][$i]['ДокументыДляПоступления'][$j]['ПредоставленаКопия'];
				break;
			}
		}
	}
}

//dump($context);

$dompdf = new Dompdf();

$dompdf->loadHtml($twig->render('priem/benefits.twig', $context));

$options = $dompdf->getOptions();

$options->set('enable_remote', true);
//$options->set('defaultFont', 'dejavu sans');

$dompdf->setOptions($options);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('benefit_' .$company . '_' . $benefit . '.pdf', ['Attachment' => 0]);
