<?
/*
SendlerTelegramm by Stanislav Valov

Сейчас стоит: ShidoPromoBot
https://api.telegram.org/bot1875602467:AAEt7expPaMyA3BxVF55qpvSF4e8XLtNrx4/setwebhook?url=https://shido.ru/tbotsendler/telegramm_sendler.php
*/
//Подключаем CModule
require("/home/s/sushish2ru/shido_new/public_html/bitrix/header.php");

//Подключаем файл с функциями
require ('functions.php'); 

sendTelegram('Читаю отправку в Телегу');

//На всякий случай оставлю прием вебхуков
$data = json_decode(file_get_contents('php://input'), TRUE);

//Получаем сообщение
$message = $data['message']['text'];
$chat_id = $data['message']['from']['id'];

//Данные для callback функций
$callback_query = $data['callback_query']['data'];
$callback_chat_id = $data['callback_query']['from']['id'];

$phone_number = $data['message']['contact']['phone_number'];
$first_name = $data['message']['chat']['first_name'];
$last_name = $data['message']['chat']['last_name'];
$userlogin = "@" . $data['message']['chat']['username'];
$username = "@" . $data['message']['chat']['username'];

//Подключение к инфоблоку управленя ботом и приветственным сообщением для регистрации в боте
$contorosElements = [];
CModule::IncludeModule("iblock");
$arSelect = Array("ID", "IBLOCK_ID","NAME","PREVIEW_TEXT","PROPERTY_T_NAME","PROPERTY_T_TOKEN","PROPERTY_T_HELLO","PROPERTY_T_REGISTER","PROPERTY_T_BUTTEXT_ONE","PROPERTY_T_BUTTEXT_TWO","PROPERTY_T_YES_REG","PROPERTY_T_REGWIN");
$arFilter = Array("IBLOCK_ID" => 21);
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);

while($element = $res->GetNext()) {

    $contorosElements = array(
        'BOT_NAME' => $element['PROPERTY_T_NAME_VALUE'],
        'BOT_TOKEN' => $element['PROPERTY_T_TOKEN_VALUE'],
        'BOT_HELLO' => $element['PROPERTY_T_HELLO_VALUE'],
        'BOT_REGISTER_TEXT' => $element['PROPERTY_T_REGISTER_VALUE'],
        'BOT_TEXT_BONE' => $element['PROPERTY_T_BUTTEXT_ONE_VALUE'],
        'BOT_TEXT_BTWO' => $element['PROPERTY_T_BUTTEXT_TWO_VALUE'],
        'BOT_TEXT_YESREG' => $element['PROPERTY_T_YES_REG_VALUE'],
        'BOT_REGWIN' => $element['PROPERTY_T_REGWIN_VALUE']
    );
}

$token_bot = $contorosElements['BOT_TOKEN'];
$name_bot = $contorosElements['BOT_NAME'];
$text_messages = mb_convert_encoding($contorosElements['BOT_HELLO'], "UTF-8");  
$text_register = mb_convert_encoding($contorosElements['BOT_REGISTER_TEXT'], "UTF-8");  
$text_but_one = mb_convert_encoding($contorosElements['BOT_TEXT_BONE'], "UTF-8");  
$text_but_two = mb_convert_encoding($contorosElements['BOT_TEXT_BTWO'], "UTF-8");  
$text_yes_reg = mb_convert_encoding($contorosElements['BOT_TEXT_YESREG'], "UTF-8"); 
$text_regwin = mb_convert_encoding($contorosElements['BOT_REGWIN'], "UTF-8");

if($message === '/start'){

    $method = 'sendMessage';
    $send_data = [
        'text' => $text_messages,
        'chat_id' => $chat_id
    ];
    sendTelegramForSendler($token_bot, $method, $send_data);

    
    $search_register = regСheck($username);

    if($search_register['IDENT_CLIENTS'] == 'N'){

        sleep(1);

        $method = 'sendMessage';

        $inline_button1 = array("text" => $text_but_one, "callback_data" => "/yes_register");
        $inline_button2 = array("text" => $text_but_two, "callback_data" => "/no_register");
        $inline_keyboard = [[$inline_button1,$inline_button2]];

        $send_data = [
            'text' => $text_register,
            'chat_id' => $chat_id,
            'reply_markup' => [ 'inline_keyboard' => $inline_keyboard ]    
        ]; 
        sendTelegramForSendler($token_bot, $method, $send_data);

    } else {

        sleep(1);

        $method = 'sendMessage';
        $send_data = [
            'text' => $text_yes_reg,
            'chat_id' => $chat_id   
        ]; 
        sendTelegramForSendler($token_bot, $method, $send_data);


    }

}

if($callback_query == '/no_register'){
    $method = 'sendMessage';
    $send_data = [
        'text' => "😎 Чтож, пройдете регистрацию в следующий раз!",
        'chat_id' => $callback_chat_id,
    ]; 
    sendTelegramForSendler($token_bot, $method, $send_data);
    sleep(1);
    $method = 'sendMessage';
    $send_data = [
        'text' => "😄 Или все-таки нажмите да, и пройдите регистрацию сейчас!",
        'chat_id' => $callback_chat_id,
    ]; 
    sendTelegramForSendler($token_bot, $method, $send_data);
}

if($callback_query == '/yes_register'){
    $method = 'sendMessage';
    $send_data = [
        'text' => "Для завершения регистрации разрешите нам\nполучить номер Вашего телефона, для включения двухэтапной авторизации, это нужно для безопасности вашего аккаунта!",
        'chat_id' => $callback_chat_id,
        'reply_markup' => [ 'resize_keyboard' => true, "one_time_keyboard" => true,  'keyboard' => [
            [[
                'text' => 'Разрешить',
                'request_contact' => true,
                
            ]]

        ]]
    ]; 
    sendTelegramForSendler($token_bot, $method, $send_data);
  
}

//sendTelegram($phone_number);

//Условие получения контактных данных при регистрации
if(strlen($phone_number) > 0){
    $checkForReqPhone = 'Y';

    if($checkForReqPhone == 'Y'){

        $checkPhoneNumbers = phoneСheck($phone_number);
        
    }
}


if($checkPhoneNumbers['IDENT_CLIENTS'] == 'N'){
                                  
    $chekReg = registrationPeople($chat_id, $first_name, $last_name, $userlogin, $phone_number);

    if($chekReg['CHECK_REGISTER'] == 'Y'){

        $method = 'sendMessage';
        $send_data = [
            'text' => $text_regwin,
            'chat_id' => $chat_id   
        ]; 
        sendTelegramForSendler($token_bot, $method, $send_data);

    } else {

        $method = 'sendMessage';
        $send_data = [
            'text' => 'Ошибка регистрации!',
            'chat_id' => $chat_id   
        ]; 
        sendTelegramForSendler($token_bot, $method, $send_data);


    }

}

//Получаем список рассылок
$SendlerElements = [];
$arSelectTT = Array("ID", "IBLOCK_ID","NAME","ACTIVE","PREVIEW_TEXT","PROPERTY_T_DATASEND","PROPERTY_T_SENDLERMESSAGE");
$arFilterTT = Array("IBLOCK_ID" => 23);
$resTT = CIBlockElement::GetList(Array(), $arFilterTT, false, Array(), $arSelectTT);

while($elementTT = $resTT->GetNext()) {

    $SendlerElements = array(
        'ACTIVE' => $elementTT['ACTIVE'],
        'BOT_DATASEND' => $elementTT['PROPERTY_T_DATASEND_VALUE'],
        'BOT_SENDLERMESSAGE' => $elementTT['PROPERTY_T_SENDLERMESSAGE_VALUE']
    );

    $el = new CIBlockElement;
    $PRODUCT_ID = $element['ID'];
    $PROSUCT_ACTIVE = array("ACTIVE" => "N");
    $seanactive = $SendlerElements['ACTIVE'];
    $datasend_dirty = mb_convert_encoding($SendlerElements['BOT_DATASEND'], "UTF-8");  
    $sendlermassage = mb_convert_encoding($SendlerElements['BOT_SENDLERMESSAGE']['TEXT'], "UTF-8"); 

    $datasend = date("m.d.y H:i", strtotime($datasend_dirty));
    $dates = date("m.d.y H:i");

    if($seanactive == 'Y' && $datasend == $dates) {
        
            $sendoffers = sendlerStartTelegram($token_bot, $sendlermassage);
            $resss = $el->Update($PRODUCT_ID, $PROSUCT_ACTIVE);

        }

}

//Функция отправки сообщений в Телеграм
function sendTelegramForSendler($token_bot, $method, $send_data)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.telegram.org/bot'.$token_bot.'/'.$method,
        CURLOPT_POSTFIELDS => json_encode($send_data),
        CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"))
    ]);
    $result = curl_exec($curl);
    curl_close($curl);
};

?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>