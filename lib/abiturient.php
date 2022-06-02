<?php
/** @var string $docId */

$context = httpGetContent('Document_АнкетаАбитуриента', "Number eq '{$docId}'");
$context = $context[0] ?? [];
$context = (array) $context;

if (!$context) {
    require_once ROOT_DIR . '/modules/404.php';
    exit();
}

$context['company_name'] = COMPANY_NAME;
$context['company_name_brief'] = COMPANY_NAME_BRIEF;
$context['priem_end_date'] = END_DATE;

$context['Гражданство'] = str_replace('ГражданинРоссийскойФедерации', 'Российская Федерация', $context['Гражданство']);

$context['ДатаРождения'] = str_replace('T', ' ', $context['ДатаРождения']);
$context['ДатаРождения'] = DateTime::createFromFormat('Y-m-d H:i:s', $context['ДатаРождения'])->format('d.m.Y');

$context['ДатаОкончанияОбразовательнойОрганизации'] = str_replace('T', ' ', $context['ДатаОкончанияОбразовательнойОрганизации']);
$context['ДатаОкончанияОбразовательнойОрганизации'] = DateTime::createFromFormat('Y-m-d H:i:s', $context['ДатаОкончанияОбразовательнойОрганизации'])->format('Y');

$context['ИзучаемыйЯзык'] = httpGetContentById('Catalog_ЯзыкиНародовМира', $context['ИзучаемыйЯзык_Key']);

$context['type_doc'] = httpGetContentById('Catalog_ДокументыУдостоверяющиеЛичность', $context['ВидДокументаУдостоверяющегоЛичность_Key']);
$context['type_doc'] = $context['type_doc']['Description'] ?? '';

$context['ДатаВыдачиДокументаУдостоверяющегоЛичность'] = str_replace('T', ' ', $context['ДатаВыдачиДокументаУдостоверяющегоЛичность']);
$context['ДатаВыдачиДокументаУдостоверяющегоЛичность'] = DateTime::createFromFormat('Y-m-d H:i:s', $context['ДатаВыдачиДокументаУдостоверяющегоЛичность'])->format('d.m.Y');

$context['Author'] = httpGetContentById('Catalog_Пользователи', $context['Автор_Key']);
$context['Author'] = $context['Author']['Description'] ?? '';

$context['Date'] = str_replace('T', ' ', $context['Date']);
$context['statement_date_d'] = DateTime::createFromFormat('Y-m-d H:i:s', $context['Date'])->format('d');
$context['statement_date_m'] = DateTime::createFromFormat('Y-m-d H:i:s', $context['Date'])->format('m');
switch ($context['statement_date_m']) {
    case 1:
        $context['statement_date_m'] = 'Января';
        break;
    case 2:
        $context['statement_date_m'] = 'Феврля';
        break;
    case 3:
        $context['statement_date_m'] = 'Марта';
        break;
    case 4:
        $context['statement_date_m'] = 'Апреля';
        break;
    case 5:
        $context['statement_date_m'] = 'Мая';
        break;
    case 6:
        $context['statement_date_m'] = 'Июня';
        break;
    case 7:
        $context['statement_date_m'] = 'Июля';
        break;
    case 8:
        $context['statement_date_m'] = 'Августа';
        break;
    case 9:
        $context['statement_date_m'] = 'Сентября';
        break;
    case 10:
        $context['statement_date_m'] = 'Октября';
        break;
    case 11:
        $context['statement_date_m'] = 'Ноября';
        break;
    case 12:
        $context['statement_date_m'] = 'Декабря';
        break;
}
$context['statement_date_y'] = DateTime::createFromFormat('Y-m-d H:i:s', $context['Date'])->format('Y');

for ($i = 0; $i < count($context['ПодачаЗаявлений']); $i++) {
    $context['ПодачаЗаявлений'][$i]['ПрограммаОбучения'] = httpGetContentById('Catalog_ПрограммыСПО', $context['ПодачаЗаявлений'][$i]['ПрограммаОбучения_Key']);
    $context['ПодачаЗаявлений'][$i]['ПрограммаОбучения']['Специальность'] = httpGetContentById('Catalog_Специальности', $context['ПодачаЗаявлений'][$i]['ПрограммаОбучения']['Специальность_Key']);
    $context['ПодачаЗаявлений'][$i]['ПрограммаОбучения']['Квалификаци'] = [];
    for ($j = 0; $j < count($context['ПодачаЗаявлений'][$i]['ПрограммаОбучения']['Квалификации']); $j++) {
        $context['ПодачаЗаявлений'][$i]['ПрограммаОбучения']['Квалификаци'][$j] = httpGetContentById('Catalog_Квалификации', $context['ПодачаЗаявлений'][$i]['ПрограммаОбучения']['Квалификации'][$j]['Квалификация_Key']);
    }
}

$context['КопияАттестата'] = '';
for ($i = 0; $i < count($context['ДокументыДляПоступления']); $i++) {
    $data = httpGetContentById('Catalog_ДокументыДляПоступления', $context['ДокументыДляПоступления'][$i]['ДокументДляПоступления_Key']);
    if ($data['ТипДокумента'] == 'ДокументОбОбразовании') {
        $context['КопияАттестата'] = $context['ДокументыДляПоступления'][$i]['ПредоставленаКопия'];
        break;
    }
}

$context['Льгота'] = '';
if ($context['Льгота_Key'] != '00000000-0000-0000-0000-000000000000')
    $context['Льгота'] = httpGetContentById('Catalog_Льготы', $context['Льгота_Key']);

//dump($context);