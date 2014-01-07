<?php
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
$md5code = $_POST['md5'];
$urlPath = $_POST['url'];
$type = $_POST['type'];
function httpcopy($url, $file="", $timeout=1800,$type) {
	
	if($type == 1)
	{
		$dir = dirname(__FILE__).'/../../mobcent/data/android/';
		$file = 'Discuz_Android.apk';
	}
	else
	{
		$dir = dirname(__FILE__).'/../../mobcent/data/ios/';
		$file = 'Discuz_Ios.ipa';
	}
	!is_dir($dir) && @mkdir($dir,0755,true);
	$url = str_replace(" ","%20",$url);
	$opts = array(
			'http'=>array(
					'method'=>"GET",
					'header'=>"Accept-language: en\r\n" .
					"Cookie: foo=bar\r\n"
			)
	);
	
	$context = stream_context_create($opts);
	
	$data = file_get_contents($url, false, $context);
	$handle = fopen ( $dir.$file, 'w' );
	if ($handle) {
		fwrite ( $handle, $data  );
		fclose ( $handle );
		return true;
	} else {
		return false;
	}

}
$i =0;
$weburl = 'apkdown.php?url='.$urlPath.'&md5='.$md5code.'&type='.$type;
for($i=0;$i<5;$i++)
{
	if(httpcopy($urlPath,$file="", $timeout=1800,$type)==false)
	{
		if($i == 4)
		{
			header("Content-type: text/html; charset=".UC_DBCHARSET);
			
			echo '<form name ="" action ='.$weburl.' method =POST>';
			echo '<p>luanma</p>';
			echo "<input type = submit value = 'luanma'>";
			echo '</form>';
		}
		$i++;
	}else
	{
		if($type == 1)
		{
			$dir = dirname(__FILE__).'/../../mobcent/data/android/';
			$file = 'Discuz_Android.apk';
		}
		else
		{
			$dir = dirname(__FILE__).'/../../mobcent/data/ios/';
			$file = 'Discuz_Ios.ipa';
		}
		IF($md5code == md5_file($dir.$file))
		{
			$data['rs'] = 1;
			echo  echo_json($data); 
		}
		else 
		{
			header("Content-type: text/html; charset=".UC_DBCHARSET);
				
			echo '<form name ="" action ='.$weburl.' method =POST>';
			echo '<p>luanma</p>';
			echo "<input type = submit value ='luanma' >";
			echo '</form>';
		}
		break;
	}

}

