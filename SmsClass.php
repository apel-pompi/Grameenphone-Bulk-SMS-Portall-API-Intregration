<?php

header("content-type: text/html; charset=UTF-8");
/**
* 
*/
class SmsClass
{
	
	function toUnicodeByteString($str){
		$unicode = array();
		$values = array();
		$lookingFor = 1;
		$getWithPadding = function($val){
			$val = "0000".$val;
			return substr($val, strlen($val)-4, 4);
		};

		for ($i = 0; $i < strlen($str); $i++) {
			$thisValue = ord($str[$i]);
			if($thisValue < 128) {
				$number =  dechex($thisValue);
				$unicode[] = $getWithPadding($number);
			}else{
				if (count($values) == 0) $lookingFor = ($thisValue < 224) ? 2 : 3;
				$values[] = $thisValue;
				if (count($values) == $lookingFor) {
					$number = ($lookingFor == 3) ?
						(($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64) :
						(($values[0] % 32) * 64) + ($values[1] % 64);
					$number = dechex($number);
					$unicode[] = $getWithPadding($number);
					$values = array();
					$lookingFor = 1;
				}
			}
		}
		return implode("", $unicode);
	}

	function hasUnicode($val){
		return strlen($val) != strlen(utf8_decode($val));
	}

	function getEncoded($val){
		if ($this->hasUnicode($val)){
			return $this->toUnicodeByteString($val);
		}
		return urlencode($val);
	}

	function SendSMS($number,$message)
	{

			$username = 'enter your portal username';
			$password = 'enter your portal bulk sms password';
			$CLT = 'enter your clt';

			$number = urlencode( $number );
			$messageType = $this->hasUnicode($message)? 3: 1;
			$message = $this->getEncoded($message);
			$messageType = 
			$url = 'https://gpcmp.grameenphone.com/gpcmpbulk/messageplatform/controller.home?username='. $username .'&password='. $password .'&apicode=6&msisdn='. $number .'&countrycode=880&cli='. $CLT .'&messagetype='.$messageType.'&message='.$message.'&messageid=0';

			// Send the POST request with cURL
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
			echo "<br/>response: ".$response;
	}


}


$sms = new SmsClass();
$result = $sms->SendSMS('Your 11 digit phone no','Your message');
echo $result;

?>