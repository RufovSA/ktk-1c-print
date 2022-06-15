<?php

use RedBeanPHP\R;

/** @var Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface $cache */

$isData = $_REQUEST['isData'] ?? '1'; // Приемные компании на текущий год

if ($isData === '1') {
    $cached_string = $cache->getItem('company_' . date('Y'));
} else {
    $cached_string = $cache->getItem('companies');
}

if ($cached_string->isHit()) {
    $context = $cached_string->get();
    return;
}

if ($isData === '1') {
    $context = httpGetContent('Catalog_ПриемныеКампании', "ГодНачала eq " . date('Y'));
} else {
    $context = httpGetContent('Catalog_ПриемныеКампании');
}

$statement = httpGetContent('Document_АнкетаАбитуриента_ПодачаЗаявлений', "LineNumber eq 1");

R::setup('sqlite::memory:');

for ($i = 0; $i < count($context); $i++) {
    $context[$i]['ПланПриема'] = httpGetContent('Document_ПланПриема', "ПриемнаяКампания_Key eq guid'" . $context[$i]['Ref_Key'] . "'");
    for ($j = 0 ; $j < count($context[$i]['ПланПриема']); $j++) {
        $context[$i]['ПланПриема'][$j]['ПрограммаОбучения'] = httpGetContentById('Catalog_ПрограммыСПО', $context[$i]['ПланПриема'][$j]['ПрограммаОбучения_Key']);
        $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['Специальность'] = httpGetContentById('Catalog_Специальности', $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['Специальность_Key']);
        for ($l = 0; $l < count($context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['Квалификации']); $l++) {
            $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['Квалификации'][$l] = httpGetContentById('Catalog_Квалификации', $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['Квалификации'][$l]['Квалификация_Key']);
        }

        foreach ($statement as $item) {
            if ($item['ПрограммаОбучения_Key'] == $context[$i]['ПланПриема'][$j]['ПрограммаОбучения_Key']) {
                $_abitur = httpGetContentById('Document_АнкетаАбитуриента', $item['Ref_Key']);

                $_abitur['КопияАттестата'] = '0';
                for ($cc = 0; $cc < count($_abitur['ДокументыДляПоступления']); $cc++) {
                    $data = httpGetContentById('Catalog_ДокументыДляПоступления', $_abitur['ДокументыДляПоступления'][$cc]['ДокументДляПоступления_Key']);
                    if ($data['ТипДокумента'] == 'ДокументОбОбразовании') {
                        $_abitur['КопияАттестата'] = $_abitur['ДокументыДляПоступления'][$cc]['ПредоставленаКопия'];
                        break;
                    }
                }
                $statement_table = R::dispense( 'statement' );
                $statement_table->number = $_abitur['Number'];
                $statement_table->bal = $_abitur['СреднийБаллАттестата'];
                $statement_table->copyatistat = $_abitur['КопияАттестата'];
                $id = R::store( $statement_table );
            }
        }
        $context[$i]['ПланПриема'][$j]['Абитуриенты'] = [];
        $dates = R::find( 'statement', 'ORDER BY bal DESC' );
        $number = 0;
        foreach ($dates as $data) {
            $context[$i]['ПланПриема'][$j]['Абитуриенты'][$number]['Number'] = $data->number;
            $context[$i]['ПланПриема'][$j]['Абитуриенты'][$number]['СреднийБаллАттестата'] = $data->bal;
            $context[$i]['ПланПриема'][$j]['Абитуриенты'][$number]['КопияАттестата'] = $data->copyatistat;
            $number++;
        }

        R::nuke();
    }
}


$cached_string->set($context)->expiresAfter(300);
$cache->save($cached_string);