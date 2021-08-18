<?
/*
SendlerTelegramm by Stanislav Valov
Файл с функциями
*/

//Функция поиска пользователя по номеру телефона в базе пользователей
function regСheck($username){

	$user_name = $username;

    $host = 'localhost';
    $dbname = 'sushish2ru_new';
    $user = 'sushish2ru_new';
    $pass = 'FDa3q4324';

	$link = mysqli_connect($host, $user, $pass, $dbname);
	$link->set_charset("utf8");

	$query = mysqli_query($link, "SELECT * FROM st_users WHERE t_login='$user_name'");
	$myrow = mysqli_fetch_array($query);

	$identClientsLogin = [];
    if (empty($myrow['t_login'])) {
	    $login_ident = 'N';
	    $identClientsLogin = array ( 'IDENT_CLIENTS' => $login_ident );
	}

    if (!empty($myrow['t_login'])) {
	    $login_ident = 'Y';
	    $identClientsLogin = array ( 'IDENT_CLIENTS' => $login_ident );

	}

	return $identClientsLogin;

}

//Функция поиска пользователя по номеру телефона в базе пользователей
function phoneСheck($phone_number){

	$reg_phone = $phone_number;

    $host = 'localhost';
    $dbname = 'sushish2ru_new';
    $user = 'sushish2ru_new';
    $pass = 'FDa3q4324';

	$link = mysqli_connect($host, $user, $pass, $dbname);
	$link->set_charset("utf8");

	$query = mysqli_query($link, "SELECT * FROM st_users WHERE phone_number='$reg_phone'");
	$myrow = mysqli_fetch_array($query);

	$identClientsPhone = [];
    if (empty($myrow['phone_number'])) {
	    $phone_ident = 'N';
	    $identClientsPhone = array ( 'IDENT_CLIENTS' => $phone_ident );
	}

    if (!empty($myrow['phone_number'])) {
	    $phone_ident = 'Y';
	    $identClientsPhone = array ( 'IDENT_CLIENTS' => $phone_ident );

	}

	return $identClientsPhone;

}

//Функция Регистрации пользователя в главной БД юзеров
function registrationPeople($chat_id, $first_name, $last_name, $userlogin, $phone_number){

    $t_id = $chat_id;
    $username = $first_name;
    $lastname = $last_name;
    $t_login = $userlogin;
    $tphone_numbers = $phone_number;

    $host = 'localhost';
    $dbname = 'sushish2ru_new';
    $user = 'sushish2ru_new';
    $pass = 'FDa3q4324';
    $db_table = 'st_users';

    try {
        // Подключение к базе данных
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        print_r($db);
        // Устанавливаем корректную кодировку
        $db->exec("set names utf8");
    } catch (PDOException $e) {
        // Если есть ошибка соединения, выводим её
        
    }

    // Собираем данные для запроса
    $data = array('t_id' => $t_id, 'username' => $username, 'lastname' => $lastname, 't_login' => $t_login, 'phone_number' => $tphone_numbers); 

    // Подготавливаем SQL-запрос
    $query = $db->prepare("INSERT INTO $db_table (t_id, username, lastname, t_login, phone_number) values (:t_id, :username, :lastname, :t_login, :phone_number)");

    // Выполнем запрос с данными
    $result = $query->execute($data);

    $checkRegister = [];
    if ($result == '1') {
        $checkRegister = array('CHECK_REGISTER' => 'Y');
    } else {
        $checkRegister = array('CHECK_REGISTER' => 'N', 'RESULT_REGISTER' => $result);

    }

    return $checkRegister;
} 

//Функция рассылки по всем зарегестрированным пользователям
function sendlerStartTelegram($token_bot, $sendlermassage){

    $text_sendler = $sendlermassage;

    $host = 'localhost';
    $dbname = 'sushish2ru_new';
    $user = 'sushish2ru_new';
    $pass = 'FDa3q4324';

    $link = mysqli_connect($host, $user, $pass, $dbname);
    $link->set_charset("utf8");

    $query = mysqli_query($link, "SELECT * FROM st_users ORDER BY t_id DESC");
    //$myrow = mysqli_fetch_array($query);

    $sendChatID = [];
    while ($myrow = mysqli_fetch_array($query)) {

        $sendChatID = array( 'SEND_ID' => $myrow['t_id'].',' );

    }

    $chat_id = $sendChatID['SEND_ID'];
    //print_r($chat_id);
    //sendTelegram(serialize($chat_id));
    sendForId($chat_id,$text_sendler);

}

function sendForId($chat_id,$text_sendler)
{

    //sendTelegram($text_sendler);    

    $send_data = [
        'text' => $text_sendler,
        'chat_id' => $chat_id,
    ]; 

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.telegram.org/bot1875602467:AAEt7expPaMyA3BxVF55qpvSF4e8XLtNrx4/sendMessage',
        CURLOPT_POSTFIELDS => json_encode($send_data),
        CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"))
    ]);
    $result = curl_exec($curl);
    curl_close($curl);
};

function sendSMSBase($diff_phone_sends, $text_sms){

    //sendTelegram("Функция запущена");
    $strokePhoneSendler = $diff_phone_sends;
    $arrPhones = explode(',',$strokePhoneSendler);

    foreach($arrPhones as $itemS) {
        if(strlen($itemS) != 0){
            $phones_sends_value[] = 'numbers[]='.$itemS.'&';
        }       
    }



    $strokeS = join($phones_sends_value);
    $text_smsS = str_replace (' ','+',$text_sms);


    //sendTelegram($strokeS);
    //sendTelegram($text_smsS);
    

    $login = 'taurus_25@mail.ru';    
    $key   = 'JsdMSTzOVFLgdvAlq1FFWjzHqX4A';    
    $sign  = 'SMS Aero'; 

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_USERPWD, $login . ':' . $key);
    curl_setopt($ch, CURLOPT_URL, 'https://gate.smsaero.ru/v2/sms/send?'.$strokeS.'text='.$text_smsS.'&sign='.$sign);
    $res = curl_exec($ch);
    curl_close($ch);
     
    $array = json_decode($res, true);

}


?>