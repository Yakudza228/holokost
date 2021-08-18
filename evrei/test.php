<?

	$strokePhoneSendler = '79131898906,89999999999,';


	$arrPhones = explode(',',$strokePhoneSendler);
	$last = array_pop($arrPhones);
	foreach($arrPhones as $itemS) {

		if(strlen($itemS) != 0){

			$phones_sends_value[] = 'numbers[]='.$itemS.'&';

		}

	   
	}


	echo join($phones_sends_value);

	$text_smsS = 'Тут собственно СМС рассылка!';
	$text_smsS = str_replace (' ','+',$text_smsS);
	echo $text_smsS;
		



	function sendSMSS($stroke, $text_sms)
	{
	    $curl = curl_init();
	    curl_setopt_array($curl, [
	        CURLOPT_POST => 1,
	        CURLOPT_HEADER => 0,
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_URL => 'https://email:api_key@gate.smsaero.ru/v2/sms/send?'.$stroke.'text='.$text.'&sign=SMS Aero',
	        CURLOPT_POSTFIELDS => json_encode($send_data),
	        CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"))
	    ]);
	    $result = curl_exec($curl);
	    curl_close($curl);
	};

?>       
        