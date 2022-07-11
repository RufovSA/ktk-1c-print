<?php

use RedBeanPHP\R;

/** @var Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface $cache */

$is_import = $is_import ?? null;

$isData = $_REQUEST['isData'] ?? '1'; // Приемные компании на текущий год
$noCache = $_REQUEST['noCache'] ?? false;

if ($isData === '1') {
    $cached_string = $cache->getItem('company_' . date('Y') . $is_import);
} else {
    $cached_string = $cache->getItem('companies' . $is_import);
}

if ($cached_string->isHit() && !$noCache) {
    $context = $cached_string->get();
    return;
}

if ($isData === '1') {
    $context = httpGetContent('Catalog_ПриемныеКампании', "ГодНачала eq " . date('Y'));
} else {
    $context = httpGetContent('Catalog_ПриемныеКампании');
}

//$statement = httpGetContent('Document_АнкетаАбитуриента_ПодачаЗаявлений', "LineNumber eq 1");
$statement = httpGetContent('Document_АнкетаАбитуриента_ПодачаЗаявлений');

R::setup('sqlite::memory:');

for ($i = 0; $i < count($context); $i++) {
    $context[$i]['ВсегоПосле9клКоп'] = 0;
    $context[$i]['ВсегоПосле9клОриг'] = 0;
    //$context[$i]['ВсегоПосле9клИтог'] = 0;
    $context[$i]['ВсегоПосле11клКоп'] = 0;
    $context[$i]['ВсегоПосле11клОриг'] = 0;
    //$context[$i]['ВсегоПосле11клИтог'] = 0;
    $context[$i]['ВсегоПолученоЗаявленийКоп'] = 0;
    $context[$i]['ВсегоПолученоЗаявленийОриг'] = 0;
    $context[$i]['ВсегоПолученоЗаявленийИтог'] = 0;
    $context[$i]['ПланПриема'] = httpGetContent('Document_ПланПриема', "ПриемнаяКампания_Key eq guid'" . $context[$i]['Ref_Key'] . "'");
    for ($j = 0 ; $j < count($context[$i]['ПланПриема']); $j++) {
        $context[$i]['ПланПриема'][$j]['ПрограммаОбучения'] = httpGetContentById('Catalog_ПрограммыСПО', $context[$i]['ПланПриема'][$j]['ПрограммаОбучения_Key']);
        $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['Специальность'] = httpGetContentById('Catalog_Специальности', $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['Специальность_Key']);
        for ($l = 0; $l < count($context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['Квалификации']); $l++) {
            $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['Квалификации'][$l] = httpGetContentById('Catalog_Квалификации', $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['Квалификации'][$l]['Квалификация_Key']);
        }

        $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['КолВоКопий'] = 0;
        $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['КолВоОригиналов'] = 0;
        foreach ($statement as $item) {
            if ($item['ПрограммаОбучения_Key'] == $context[$i]['ПланПриема'][$j]['ПрограммаОбучения_Key']) {
                $_abitur = httpGetContentById('Document_АнкетаАбитуриента', $item['Ref_Key']);
                if (!$_abitur['ДокументыВозвращены']) {
                    $_abitur['КопияАттестата'] = '0';
                    if ($item['LineNumber'] == '1') {
                        for ($cc = 0; $cc < count($_abitur['ДокументыДляПоступления']); $cc++) {
                            $data = httpGetContentById('Catalog_ДокументыДляПоступления', $_abitur['ДокументыДляПоступления'][$cc]['ДокументДляПоступления_Key']);
                            if ($data['ТипДокумента'] == 'ДокументОбОбразовании') {
                                $_abitur['КопияАттестата'] = $_abitur['ДокументыДляПоступления'][$cc]['ПредоставленаКопия'];
                                break;
                            }
                        }
                    } else {
                        $_abitur['КопияАттестата'] = '1';
                    }

                    if ($_abitur['КопияАттестата']) $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['КолВоКопий']++;
                    else $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['КолВоОригиналов']++;
                    $statement_table = R::dispense( 'statement' );
                    $statement_table->number = $_abitur['Number'];
                    $statement_table->bal = $_abitur['СреднийБаллАттестата'];
                    $statement_table->LineNumber = $item['LineNumber'];
                    $statement_table->copyatistat = $_abitur['КопияАттестата'];
                    if ($is_import) $statement_table->data = json_encode($_abitur);
                    $id = R::store( $statement_table );
                }
            }
        }
        if ($context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['БазовоеОбразование'] == 'ОсновноеОбщее') {
            $context[$i]['ВсегоПосле9клКоп'] += $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['КолВоКопий'];
            $context[$i]['ВсегоПосле9клОриг'] += $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['КолВоОригиналов'];
            //$context[$i]['ВсегоПосле9клИтог'] += $context[$i]['ВсегоПосле9клКоп'] + $context[$i]['ВсегоПосле9клОриг'];
        } else {
            $context[$i]['ВсегоПосле11клКоп'] += $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['КолВоКопий'];
            $context[$i]['ВсегоПосле11клОриг'] += $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['КолВоОригиналов'];
            //$context[$i]['ВсегоПосле11клИтог'] += $context[$i]['ВсегоПосле11клКоп'] + $context[$i]['ВсегоПосле11клОриг'];
        }

        $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['ИтогоЗаявлений'] = $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['КолВоКопий'] + $context[$i]['ПланПриема'][$j]['ПрограммаОбучения']['КолВоОригиналов'];
        $context[$i]['ПланПриема'][$j]['Абитуриенты'] = [];
        $dates = R::find( 'statement', 'ORDER BY bal DESC' );
        $number = 0;
        foreach ($dates as $data) {
            $context[$i]['ПланПриема'][$j]['Абитуриенты'][$number]['Number'] = $data->number;
            $context[$i]['ПланПриема'][$j]['Абитуриенты'][$number]['СреднийБаллАттестата'] = $data->bal;
            $context[$i]['ПланПриема'][$j]['Абитуриенты'][$number]['LineNumber'] = $data->LineNumber;
            $context[$i]['ПланПриема'][$j]['Абитуриенты'][$number]['КопияАттестата'] = $data->copyatistat;
            if ($is_import) $context[$i]['ПланПриема'][$j]['Абитуриенты'][$number]['data'] = json_decode($data->data, true);
            $number++;
        }

        R::nuke();
    }
    $context[$i]['ВсегоПолученоЗаявленийКоп'] += $context[$i]['ВсегоПосле9клКоп'] + $context[$i]['ВсегоПосле11клКоп'];
    $context[$i]['ВсегоПолученоЗаявленийОриг'] += $context[$i]['ВсегоПосле9клОриг'] + $context[$i]['ВсегоПосле11клОриг'];
    $context[$i]['ВсегоПолученоЗаявленийИтог'] += $context[$i]['ВсегоПолученоЗаявленийКоп'] + $context[$i]['ВсегоПолученоЗаявленийОриг'];
}
$context['updateTime'] = date('d.m.Y H:i:s');

//dump($context);
$cached_string->set($context)->expiresAfter(31536000);
$cache->save($cached_string);