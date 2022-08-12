<?php

use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\PhpWord;
use RedBeanPHP\R;

/** @var array $context */

$company = $_REQUEST['company'] ?? null;
$debug = $_REQUEST['debug'] ?? null;
$pnNum = $_REQUEST['pnNum'] ?? null;

if (!$company) {
	require_once ROOT_DIR . '/modules/404.php';
	exit();
}

$context = [];

$context['CountTotal'] = 0;
$context['company'] = httpGetContent('Catalog_ПриемныеКампании', "Code eq '{$company}'");
if (!$context['company']) {
	require_once ROOT_DIR . '/modules/404.php';
	exit();
}
$context['company'] = $context['company'][0];

R::setup('sqlite::memory:');

$context['plan'] = httpGetContent('Document_ПланПриема', "ПриемнаяКампания_Key eq guid'" . $context['company']['Ref_Key'] . "'");

$docInfo = [];
foreach (httpGetContent('Catalog_ДокументыДляПоступления') as $item) {
	if ($item['ТипДокумента'] == 'ДокументОбОбразовании') {
		$docInfo[$item['Ref_Key']] = $item['Description'];
	}
}


$specialties = [];
foreach (httpGetContent('Catalog_Специальности') as $item) {
	if (!$item['IsFolder']) $specialties[$item['Ref_Key']] = $item['Code'] . '«' . $item['Description'] . '»';
}

$year = $context['company']['ГодНачала'];
$numberOrder = $_REQUEST['numberOrder'] ?? '185к';

// Создание нового документа
$phpWord = new PhpWord();

// Устновка метаданных документа
$unix = mktime(date('h'), date('i'), date('s'), date('m'), date('d'), date('Y'));
$properties = $phpWord->getDocInfo();
$properties->setCreator('Михалева Снежанна Александровна');
$properties->setCompany('ГАПОУ КО "КТК"');
$properties->setTitle('Приказ о зачислении');
$properties->setDescription('Приказ о зачислении студентов 1 курса');
$properties->setCategory('Приказы');
$properties->setLastModifiedBy('Белевская Татьяна Викторовна');
$properties->setCreated($unix);
$properties->setModified($unix);
$properties->setSubject('Приказ');
$properties->setKeywords('приказ,зачисление,1 курс,приказ о зачислении');
unset($properties);

// Параметры документа
$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(13);
$phpWord->addParagraphStyle('ГОСТ', array(
		'lineHeight' => 1.5,
		'spaceAfter' => 0,
		'spaceBefore' => 0,
		'align' => 'both'
	)
);
$phpWord->addParagraphStyle('One Paragraph', array(
		'lineHeight' => 1,
		'spaceAfter' => 0,
		'spaceBefore' => 0,
	)
);

// Создание секции для текста
$sectionStyle = array(
	'orientation' => 'portrait',
	'marginTop' => Converter::cmToTwip(2),
	'marginLeft' => Converter::cmToTwip(2.5),
	'marginRight' => Converter::cmToTwip(1),
	'marginBottom' => Converter::cmToTwip(1),
	'colsNum' => 1,
	'pageNumberingStart' => 1,
	/*'borderBottomSize'=>100,
	'borderBottomColor'=>'C0C0C0'*/
	'spacingLineRule' => \PhpOffice\PhpWord\SimpleType\LineSpacingRule::AT_LEAST,
	'lineHeight' => 1.5,
	'spaceAfter' => 0,
	'spaceBefore' => 0,
);
$section = $phpWord->addSection($sectionStyle);

$text = "Государственное автономное
профессиональное образовательное
учреждение Калужской области
«Калужский технический колледж»
(ГАПОУ КО «КТК»)

ПРИКАЗ

«16» августа " . $year . " г.		№ " . $numberOrder . "

г. Калуга

О зачислении
";
foreach (explode("\n", $text) as $txt) {
	$section->addText(htmlspecialchars($txt), ['bold' => true], 'One Paragraph');
}

$text = "	В соответствии с ФЗ «Об образовании в РФ», Правилами приема на обучение по программам профессионального обучения, дополнительного профессионального образования в ГАПОУ КО «КТК» на " . $year . "-" . ($year + 1) . " гг. от 16.08." . $year;
foreach (explode("\n", $text) as $txt) {
	$section->addText(htmlspecialchars($txt), null, 'ГОСТ');
}

$text = "1. ЗАЧИСЛИТЬ:
	с 1 сентября " . $year . " года в число студентов 1 курса очной формы обучения в рамках контрольных цифр приема за счет бюджетных ассигнований областного бюджета следующих абитуриентов:";
foreach (explode("\n", $text) as $txt) {
	$section->addText(htmlspecialchars($txt), ['bold' => true], 'ГОСТ');
}

$i = 1;
foreach ($context['plan'] as $plan) {
	$context['CountTotal'] += $plan['Данные'][0]['План'];
	$education = 'основного общего';
	if ($plan['Данные'][0]['ПолученноеОбразование'] == 'СреднееОбщее') $education = 'среднего  общего';
	$text = "	1.{$i}. По специальности " . $specialties[$plan['Специальность_Key']] . " на базе {$education} образования:";
	$section->addText(htmlspecialchars($text), ['bold' => true], 'One Paragraph');

	foreach (httpGetContent('Document_АнкетаАбитуриента', "Специальность_Key eq guid'{$plan['Специальность_Key']}'") as $item) {
		if ($item['ПодачаЗаявлений'][0]['ПрограммаОбучения_Key'] != $plan['ПрограммаОбучения_Key']) {
			continue;
		}
		foreach ($item['ДокументыДляПоступления'] as $doc) {
			if (isset($docInfo[$doc['ДокументДляПоступления_Key']]) && $doc['ПредоставленаКопия'] != '1' && $item['ДокументыВозвращены'] == '') {
				$myBal = $item['СреднийБаллАттестата'];
				if ($item['Комментарий'] == 'АВН') {
					$myBal += 1.2;
					if ($myBal >= 4.5) {
						$myBal = 4.5;
					}
				}
				$statement_table = R::dispense('statement');
				$statement_table->number = $item['Number'];
				$statement_table->fio = $item['Фамилия'] . ' ' . $item['Имя'] . ' ' . $item['Отчество'];
				$statement_table->bal = $myBal;
				R::store($statement_table);
			}
		}
	}

	$dates = R::find('statement', 'ORDER BY bal DESC');
	$num = 1;
	foreach ($dates as $item) {
		$statement_table = R::dispense('statement2');
		$statement_table->number = $item->number;
		$statement_table->fio = $item->fio;
		$statement_table->bal = $item->bal;
		R::store($statement_table);
		if ($plan['Данные'][0]['План'] == $num) break;
		$num++;
	}

	$dates = R::find('statement2', 'ORDER BY fio ASC');

	$table = $section->addTable();
	$num = 1;
	foreach ($dates as $item) {
		if ($debug) $pnNum = $item->number;
		$table->addRow();
		$table->addCell()->addText($num . '. ', ['bold' => true], 'One Paragraph');
		$table->addCell()->addText($item->fio, null, 'One Paragraph');
		$table->addCell()->addText('п/н '. $pnNum, null, 'One Paragraph');
		$table->addCell()->addText('', null, 'One Paragraph');
		//if ($plan['Данные'][0]['План'] == $num) break;
		$num++;
		if ($pnNum && !$debug) $pnNum++;
	}


	$text = "Всего: " . $plan['Данные'][0]['План'] . " человек";
	$section->addText(htmlspecialchars($text), ['bold' => true], 'ГОСТ');
	R::nuke();
	$i++;
}

$text = "
ИТОГО: {$context['CountTotal']} человек

2. ВНЕСТИ:";
foreach (explode("\n", $text) as $txt) {
	$section->addText(htmlspecialchars($txt), ['bold' => true], 'ГОСТ');
}

$text = "	зам. директора по УТР запись в «Поименную книгу студентов» дневного (бюджетного) отделения\n";
foreach (explode("\n", $text) as $txt) {
	$section->addText(htmlspecialchars($txt), null, 'ГОСТ');
}

$text = "Директор колледжа								А.В.Никитин";
foreach (explode("\n", $text) as $txt) {
	$section->addText(htmlspecialchars($txt), ['bold' => true], 'ГОСТ');
}

// Отдача документа
header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="Приказ о зачислении ' . $year . ' г..docx"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save("php://output");
