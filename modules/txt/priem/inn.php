<?php
if (
    !isset($_POST['fam']) ||
    !isset($_POST['nam']) ||
    !isset($_POST['otch']) ||
    !isset($_POST['bdate']) ||
    !isset($_POST['docno']) ||
    !isset($_POST['docdt'])
) {
    require_once ROOT_DIR . '/modules/404.php';
    exit();
}

function suggestInn($surname, $name, $patronymic, $birthdate, $doctype, $docnumber, $docdate)
{
    $url = "https://service.nalog.ru/inn-proc.do";
    $data = array(
        "fam" => $surname,
        "nam" => $name,
        "otch" => $patronymic,
        "bdate" => $birthdate,
        "bplace" => "",
        "doctype" => $doctype,
        "docno" => $docnumber,
        "docdt" => $docdate,
        "c" => "innMy",
        "captcha" => "",
        "captchaToken" => ""
    );
    /*$options = array(
        'http' => array(
            'method' => 'POST',
            'header' => array(
                'Content-type: application/x-www-form-urlencoded',
            ),
            'content' => http_build_query($data)
        ),
    );


    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);*/
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

$d = substr($_POST['docno'], 0, 2);
$d2 = substr($_POST['docno'], 2);
$_POST['docno'] = $d . ' ' . $d2;

$_POST['bdate'] = str_replace('0:00:00', '', $_POST['bdate']);
$_POST['docdt'] = str_replace('0:00:00', '', $_POST['docdt']);

$data = json_decode(suggestInn(
    $_POST['fam'],
    $_POST['nam'],
    $_POST['otch'],
    $_POST['bdate'],
    21,
    $_POST['docno'],
    $_POST['docdt'],
), true);

if (isset($data['inn'])) {
    echo $data['inn'];
}
