<?php
class CV_SECURE
{
	public static function encrypt($string, $key)
	{	
		$result = '';

		for($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result .= $char;
		}
		return rtrim(strtr(base64_encode($result), '+/', '-_'), '=');
	}
	public static function decrypt($string, $key)
	{	
		$result = '';
		$string =  base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', 1));
	  
		for($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result .= $char;
		}
	  
		return $result;
	}
}
