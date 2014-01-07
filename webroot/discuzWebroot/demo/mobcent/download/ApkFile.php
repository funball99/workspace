<?php
$platForm = php_uname('s');
$system = ucfirst($platForm);

if($system =='Linux'){
	$pos = explode('/', dirname(__FILE__));
	foreach($pos as $key=>$val)
	{
		if(count($pos)-1 <=$key)
			continue;
		$str .= $pos[$key].'/';
	}
}else{
	$url =dirname(__FILE__);
	$str = str_replace('download', '', $url);
}

switch($_GET['p'])
{
	case 1:
		$filename = $str.'data/android/Discuz_Android.apk';
		$file = fopen($filename,"r");  
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length: ".@filesize($filename));
		Header("Content-Disposition: attachment; filename=DiscuzAndroid.apk");
		
		readfile($filename);
		exit();
		
	case 2:
		$filename = $str.'data/ios/Discuz_Ios.ipa';
		$file = fopen($filename,"r");  
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length: ".@filesize($filename));
		Header("Content-Disposition: attachment; filename=DiscuzIos.ipa");
		readfile($filename);
		exit();
	case 'ipa':
			$filename = $str.'/data/ios/Discuz_Ios.ipa';
					
			$file = fopen($filename,"r");
			header("Content-type: application/octet-stream");
			header("Accept-Ranges: bytes");
			Header("Content-type: application/force-download");
			Header("Accept-Length: ".@filesize($filename));
			Header("Content-Disposition: attachment; filename=DiscuzIos.ipa");
		
			readfile($filename);
			exit();
}