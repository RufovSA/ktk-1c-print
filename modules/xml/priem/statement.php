<?php
/** Формирование пакета ФИС для импорта Заявлений */

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

$regions = httpGetContent('Catalog_РегионыРФ');

$paket = httpGetContent('Document_ПакетФИС', "Number eq '{$_REQUEST['doc_id']}'");
unset(
    $paket['DataVersion'],
    $paket['DeletionMark'],
    $paket['Организация_Key'],
    $paket['ДатаПриемнойКампании_Key'],
    $paket['Статус'],
    $paket['ТестовыйРежим'],
    $paket['РезультатВалидации'],
    $paket['ИдентификатораПакета'],
    $paket['Комментарий'],
    $paket['РезультатПроверкиДанных_Type'],
    $paket['РезультатПроверкиДанных_Base64Data'],
    $paket['РезультатОПроверкиПолучен'],
    $paket['Posted'],
    $paket['ХранилищеДанных_Type'],
    $paket['Организация@navigationLinkUrl'],
    $paket['ПриемнаяКампания@navigationLinkUrl'],
    $paket['ДатаПриемнойКампании@navigationLinkUrl'],
    $paket['ХранилищеДанных_Base64Data']
);
$paket = $paket[0];
$infoCompany = httpGetContentById('Catalog_ПриемныеКампании', $paket['ПриемнаяКампания_Key']);

$data['year'] = $infoCompany['ГодНачала'];

$data['statements'] = [];
for ($i = 0; $i < count($paket['Анкеты']); $i++) {
    $data['statements'][$i] = httpGetContentById('Document_АнкетаАбитуриента', $paket['Анкеты'][$i]['Анкета_Key']);

    $data['statements'][$i]['СтраховойНомерПФР'] = str_replace('-', '', $data['statements'][$i]['СтраховойНомерПФР']);
    $data['statements'][$i]['СтраховойНомерПФР'] = str_replace(' ', '', $data['statements'][$i]['СтраховойНомерПФР']);

    $data['statements'][$i]['OriginalReceivedDate'] = str_replace('T', ' ', $data['statements'][$i]['Date']);
    $data['statements'][$i]['OriginalReceivedDate'] = DateTime::createFromFormat('Y-m-d H:i:s', $data['statements'][$i]['OriginalReceivedDate'])->format('Y-m-d');

    $data['statements'][$i]['ДатаРождения'] = str_replace('T', ' ', $data['statements'][$i]['ДатаРождения']);
    $data['statements'][$i]['ДатаРождения'] = DateTime::createFromFormat('Y-m-d H:i:s', $data['statements'][$i]['ДатаРождения'])->format('Y-m-d');

    $data['statements'][$i]['ДатаОкончанияОбразовательнойОрганизации'] = str_replace('T', ' ', $data['statements'][$i]['ДатаОкончанияОбразовательнойОрганизации']);
    $data['statements'][$i]['ДатаОкончанияОбразовательнойОрганизации'] = DateTime::createFromFormat('Y-m-d H:i:s', $data['statements'][$i]['ДатаОкончанияОбразовательнойОрганизации'])->format('Y-m-d');
    $data['statements'][$i]['EndYear'] = substr($data['statements'][$i]['ДатаОкончанияОбразовательнойОрганизации'], 0, 4);

    $data['statements'][$i]['ДатаВыдачиДокументаУдостоверяющегоЛичность'] = str_replace('T', ' ', $data['statements'][$i]['ДатаВыдачиДокументаУдостоверяющегоЛичность']);
    $data['statements'][$i]['ДатаВыдачиДокументаУдостоверяющегоЛичность'] = DateTime::createFromFormat('Y-m-d H:i:s', $data['statements'][$i]['ДатаВыдачиДокументаУдостоверяющегоЛичность'])->format('Y-m-d');

    $data['statements'][$i]['ReleaseCountryID'] = 1;
    if ($data['statements'][$i]['Гражданство'] == 'ГражданинРоссийскойФедерации')
        $data['statements'][$i]['ReleaseCountryID'] = 1;

    $data['statements'][$i]['Пол'] = $data['statements'][$i]['Пол'] == 'Женский' ? 2: 1;

    $data['statements'][$i]['Email'] = '';
    $data['statements'][$i]['RegionID'] = 40;
    $data['statements'][$i]['TownTypeID'] = 2;
    $data['statements'][$i]['Address'] = '';
    foreach ($data['statements'][$i]['КонтактнаяИнформация'] as $item) {
        switch ($item['Тип']) {
            case 'АдресЭлектроннойПочты':
                $data['statements'][$i]['Email'] = $item['Представление'];
                break;
            case 'Телефон':
                $data['statements'][$i]['Phone'] = $item['Представление'];
                break;
            case 'Адрес':
                foreach ($regions as $region) {
                    if ($region['Ref_Key'] == $data['statements'][$i]['Регион_Key']) {
                        $data['statements'][$i]['RegionID'] = $region['Code'];
                    }
                }
                switch ($data['statements'][$i]['ТипНаселеногоПункта']) {
                    case 'ГородФедеральногоЗначения':
                        $data['statements'][$i]['TownTypeID'] = 1;
                        break;
                    case 'Город':
                        $data['statements'][$i]['TownTypeID'] = 2;
                        break;
                    case 'НаселенныйПунктГородскогоТипа':
                        $data['statements'][$i]['TownTypeID'] = 3;
                        break;
                    case 'НаселенныйПунктСельскогоТипа':
                        $data['statements'][$i]['TownTypeID'] = 4;
                        break;
                }
                $data['statements'][$i]['Address'] = $item['Представление'];
                break;
        }
    }

    $data['statements'][$i]['КопияАттестата'] = '';
    for ($j = 0; $j < count($data['statements'][$i]); $j++) {
        if ($data['statements'][$i]['ДокументыДляПоступления'][$j]['ДокументДляПоступления_Key'] != '00000000-0000-0000-0000-000000000000') {
            $data1 = httpGetContentById('Catalog_ДокументыДляПоступления', $data['statements'][$i]['ДокументыДляПоступления'][$j]['ДокументДляПоступления_Key']);
            if ($data1['ТипДокумента'] == 'ДокументОбОбразовании') {
                $data['statements'][$i]['КопияАттестата'] = $data['statements'][$i]['ДокументыДляПоступления'][$j]['ПредоставленаКопия'];
                break;
            }
        }
    }

    $data['statements'][$i]['ПодачаЗаявлений'] = $data['statements'][$i]['ПодачаЗаявлений'][0];
    $po = httpGetContentById('Catalog_ПрограммыСПО', $data['statements'][$i]['ПодачаЗаявлений']['ПрограммаОбучения_Key']);
    $data['statements'][$i]['Специальность'] = httpGetContentById('Catalog_Специальности', $po['Специальность_Key']);

}


$data['fis_login'] = FIS_LOGIN;
$data['fis_pass'] = FIS_PASS;

//dump($data);

header('Content-Type: application/xml; charset=utf-8');
$html = $twig->render('priem/fis/statement.twig', $data);

if (!DEBUG) {
    echo Optimize::html($html);
} else {
    echo $html;
}

