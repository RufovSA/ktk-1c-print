<?php
/**
 * Полный пакет
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

/**
 * Заявление абитуриента
 */
$html = $twig->render('priem/statement.twig', $context);
$html = str_replace('</body>', '', $html);
$html = str_replace('</html>', '', $html);
$html .= '<div style="page-break-before: always;"></div>';

/**
 * Согласие на обработку персональных данных
 */
$date = explode('.', $context['ДатаРождения']);

function getAge($y, $m, $d)
{
    if ($m > date('m') || $m == date('m') && $d > date('d'))
        return (date('Y') - $y - 1);
    else
        return (date('Y') - $y);
}

$context['Age'] = getAge($date[2], $date[1], $date[0]);

$context['parent'] = $context['СоставСемьи'][0] ?? null;

if ($context['parent']) {
    if ($context['parent']['ВидДокументаУдостоверяющегоЛичность_Key'] != '00000000-0000-0000-0000-000000000000') {
        $context['parent']['ВидДокументаУдостоверяющегоЛичность'] = httpGetContentById('Catalog_ДокументыУдостоверяющиеЛичность', $context['parent']['ВидДокументаУдостоверяющегоЛичность_Key']);
    }

    $context['parent']['ДатаВыдачиДокументаУдостоверяющегоЛичность'] = str_replace('T', ' ', $context['parent']['ДатаВыдачиДокументаУдостоверяющегоЛичность']);
    $context['parent']['ДатаВыдачиДокументаУдостоверяющегоЛичность'] = DateTime::createFromFormat('Y-m-d H:i:s', $context['parent']['ДатаВыдачиДокументаУдостоверяющегоЛичность'])->format('d.m.Y');

}

$data = preg_replace('#<head(.*?)>(.*?)</head>#is', '', $twig->render('priem/approval.twig', $context));
$data = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $data);
$data = preg_replace('#<script (.*?)>(.*?)</script>#is', '', $data);
$data = str_replace('<!DOCTYPE html>', '', $data);
$data = str_replace('<html lang="ru">', '', $data);
$data = str_replace('<body>', '', $data);
$data = str_replace('</body>', '', $data);
$data = str_replace('</html>', '', $data);

$html .= $data;
$html .= '<div style="page-break-before: always;"></div>';
$html .= '<div style="page-break-before: always;"></div>';


if ($context['ТребуетсяОбщежитие']) {
    /**
     * Заявление на общажитие
     */
    $nc = new \NCLNameCaseRu();
    $context['fio_p'] = $nc->q($context['Фамилия'] . ' ' . $context['Имя'] . ' ' . $context['Отчество'], \NCL::$RODITLN);
    $context['fio_d'] = $nc->q($context['Фамилия'] . ' ' . $context['Имя'] . ' ' . $context['Отчество'], \NCL::$DATELN);

    $context['data_start'] = date('Y');
    $context['data_end'] = date('Y') + 1;

    $context['parent'] = $context['СоставСемьи'][0];

    $data = preg_replace('#<head(.*?)>(.*?)</head>#is', '', $twig->render('priem/dormitory.twig', $context));
    $data = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $data);
    $data = preg_replace('#<script (.*?)>(.*?)</script>#is', '', $data);
    $data = str_replace('<!DOCTYPE html>', '', $data);
    $data = str_replace('<html lang="ru">', '', $data);
    $data = str_replace('<body>', '', $data);
    $data = str_replace('</body>', '', $data);
    $data = str_replace('</html>', '', $data);
    $html .= $data;
    $html .= '<div style="page-break-before: always;"></div>';
    $html .= '<div style="page-break-before: always;"></div>';
}

/**
 * Расписка абитуриента
 */
$nc = new \NCLNameCaseRu();

$context['ВидДокументаОбразованияКласс'] = '(11 кл.)';
if ($context['ВидДокументаОбразования'] == 'АттестатОсновноеОбщее') $context['ВидДокументаОбразованияКласс'] = '(9 кл.)';

$context['fio_r'] = $nc->q($context['Фамилия'] . ' ' . $context['Имя'] . ' ' . $context['Отчество'], \NCL::$RODITLN);
$context['ПодачаЗаявлений'] = $context['ПодачаЗаявлений'][0];

for ($i = 0; $i < count($context['ДокументыДляПоступления']); $i++) {
    $context['ДокументыДляПоступления'][$i]['ДокументДляПоступления'] = httpGetContentById('Catalog_ДокументыДляПоступления', $context['ДокументыДляПоступления'][$i]['ДокументДляПоступления_Key']);
}

$context['count_receipt'] = $_REQUEST['count'] ?? 1;

$data = preg_replace('#<head(.*?)>(.*?)</head>#is', '', $twig->render('priem/receipt.twig', $context));
$data = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $data);
$data = preg_replace('#<script (.*?)>(.*?)</script>#is', '', $data);
$data = str_replace('<!DOCTYPE html>', '', $data);
$data = str_replace('<html lang="ru">', '', $data);
$data = str_replace('<body>', '', $data);
$data = str_replace('</body>', '', $data);
$data = str_replace('</html>', '', $data);

$html .= $data;

/******************************************************************/
$html .= '</body></html>';

$dompdf->loadHtml($html);

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
