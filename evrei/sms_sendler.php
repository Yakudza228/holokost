<?
/*
SendlerSms by Stanislav Valov from smsaero.ru

Метод отправки СМС
https://taurus_25@mail.ru:JsdMSTzOVFLgdvAlq1FFWjzHqX4A@gate.smsaero.ru/v2/sms/send?numbers[]=79131898906text=your+text&sign=SMS Aero



https://taurus_25@mail.ru:JsdMSTzOVFLgdvAlq1FFWjzHqX4A@gate.smsaero.ru/v2/sms/send?numbers[]=89131898906&numbers[]=&text=your+text&sign=SMS Aero
https://taurus_25@mail.ru:JsdMSTzOVFLgdvAlq1FFWjzHqX4A@gate.smsaero.ru/v2/auth

*/

//Подключаем CModule
require("/home/s/sushish2ru/shido_new/public_html/bitrix/header.php");

//Подключаем файл с функциями
require ('functions.php'); 

sendTelegram('Читаю отправку СМС');

//Первое что делаем получаем собственно рассылку
CModule::IncludeModule("iblock");
$arSelect = Array("ID", "IBLOCK_ID","NAME","ACTIVE","PROPERTY_TS_DATASEND","PROPERTY_TS_SENDLERMESSAGE","PROPERTY_TS_PHONENUMBERS");
$arFilter = Array("IBLOCK_ID" => 22);
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);

while($element = $res->GetNext()) {


    $el = new CIBlockElement;
    $PRODUCT_IDS = $element['ID'];
    $PROSUCT_ACTIVES = array("ACTIVE" => "N");

	$seanactiveS  = $element['ACTIVE'];
	$datasendS_dirty = mb_convert_encoding($element['PROPERTY_TS_DATASEND_VALUE'], "UTF-8");
	
	$text_sms = mb_convert_encoding($element['PROPERTY_TS_SENDLERMESSAGE_VALUE']['TEXT'], "UTF-8");
	CFile::GetFileArray($element['PROPERTY_TS_PHONENUMBERS_VALUE'])['SRC'];
	$elementphones = $_SERVER['DOCUMENT_ROOT'] . CFile::GetFileArray($element['PROPERTY_TS_PHONENUMBERS_VALUE'])['SRC'];

	//Массив номеров телефонов из файла прикрепленного в инфоблок
	$dataphones = file_get_contents($elementphones);
	$searchphones = explode("\r\n", $dataphones);

	foreach ($searchphones as $value) {

		$phones_value[] = $value.',';
    	

	};

	$strokePhoneSendler = join( ', ',$phones_value);
	$datasendS = date("m.d.y H:i", strtotime($datasendS_dirty));
	$datesS = date("m.d.y H:i");

    $host = 'localhost';
    $dbname = 'sushish2ru_new';
    $user = 'sushish2ru_new';
    $pass = 'FDa3q4324';

    $link = mysqli_connect($host, $user, $pass, $dbname);
    $link->set_charset("utf8");

    $query = mysqli_query($link, "SELECT * FROM st_users ORDER BY phone_number DESC");
    //$myrow = mysqli_fetch_array($query);


	while ($myrow = mysqli_fetch_array($query)) {

		$phone_numberS = $myrow['phone_number'].',';

		$chars = ['+'];
		$phone_numberSV[] = str_replace($chars, '', $phone_numberS);



	}

	$strokePhoneBD = join($phone_numberSV);
	 
	$diff_phone_sends = array_diff(
	    explode(",",$strokePhoneSendler),
	    explode(",",$strokePhoneBD)
	);
	 

    if($seanactiveS == 'Y' && $datasendS == $datesS) {
        
    
    	$sendSMS = sendSMSBase(join($diff_phone_sends), $text_sms);
    	$resssS = $el->Update($PRODUCT_IDS, $PROSUCT_ACTIVES);

    }




}



?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>