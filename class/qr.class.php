<?php
	require_once('secure.class.php');
	class CV_QR
	{
		public static function getCode($persId, $testId)
		{	
			$cht = "qr";
			$chs = "300x300";
			$params = 'persId=' . $persId . '&testId=' .$testId;
			$verschluessseln= CV_SECURE::encrypt($params, "Wissen=M8");
			$permaLink = get_permalink() . '?ident=' . $verschluessseln;
			$chl = urlencode($permaLink);
			$choe = "UTF-8";
			$qrcode = 'https://chart.googleapis.com/chart?cht=' . $cht . '&chs=' . $chs . '&chl=' . $permaLink . '&choe=' . $choe;

	return '<img src="' .$qrcode. '" alt="Verifizierungslink QR ">';
		}
	}
