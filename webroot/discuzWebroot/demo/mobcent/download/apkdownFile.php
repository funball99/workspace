<?php 

error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once '../tool/tool.php';
try{	
	IF(file_exists('App.xml'))
	{
		unlink('App.xml');
	}
	require_once 'phpqrcode.php';
	$AppName =str_replace('\\\\', '\\', $_POST['AppName']);
	$AppcontentId =str_replace('\\\\', '\\', $_POST['contentId']);
	
	$AppDescribe =str_replace('\\\\', '\\', $_POST['AppDescribe']);
	
	$AppVersion =str_replace('\\\\', '\\', $_POST['AppVersion']);
	
	$AppAuthor =str_replace('\\\\', '\\', $_POST['AppAuthor']);
	$AppIcon =str_replace('\\\\', '\\', $_POST['AppIcon']);
	$AppIcon =Common::get_unicode_charset($AppIcon);
	
	$AppImg =str_replace('\\\\', '\\', $_POST['AppImg']);
	$AppImg =Common::get_unicode_charset($AppImg);


	$AppPlatform ='Android /iPhone';
	$Androiddir = dirname(__FILE__).'/../data/android/';
	$Iosdir =  dirname(__FILE__).'/../data/ios/';
	!is_dir($Androiddir) && @mkdir($Androiddir,0755,true);
	!is_dir($Iosdir) && @mkdir($Iosdir,0755,true);
	$pos = explode('/', $_SERVER["SERVER_NAME"]. $_SERVER["PHP_SELF"]);
	foreach($pos as $key=>$val)
	{
		if(count($pos)-2 <=$key)
			continue;
		$str .= $pos[$key].'/';
	}

	$AndroidPath = QRcode::png('http://'.$str.'download/ApkFile.php?p=1', $Androiddir.'android.png');
	$IosPath = QRcode::png('http://'.$str.'download/ApkFile.php?p=2',$Iosdir.'ios.png');
	
	
	$dom = new DOMDocument('1.0');
	
	
	$App = $dom ->createElement('App');
	$dom ->appendChild($App);
	
	/*AppID*/
	$Id =$dom ->createElement('AppID');
	$App ->appendChild($Id);
	$IdText  = $dom->createTextNode($AppcontentId);
	$Id ->appendChild($IdText);
	
	/*AppName*/
	$Name =$dom ->createElement('AppName');
	$App ->appendChild($Name);
	$NameText  = $dom->createTextNode($AppName);
	$Name ->appendChild($NameText);
	
	/*AppDescribe*/
	$Describe =$dom ->createElement('AppDescribe');
	$App ->appendChild($Describe);
	$DescribeText  = $dom->createTextNode($AppDescribe);
	$Describe ->appendChild($DescribeText);
	
	/*AppImg*/
	$DomAppImg =$dom ->createElement('AppImg');
	$App ->appendChild($DomAppImg);
	$AppImgText  = $dom->createTextNode($AppImg);
	$DomAppImg ->appendChild($AppImgText);
	
	/*author*/
	$AppAndroid =$dom ->createElement('AppAndroid');
	$App ->appendChild($AppAndroid);
	$AppAndroidText  = $dom->createTextNode($AppAuthor);
	$AppAndroid ->appendChild($AppAndroidText);
	
	/*version*/
	$DomAppVersion =$dom ->createElement('AppVersion');
	$App ->appendChild($DomAppVersion);
	$AppVersionText  = $dom->createTextNode($AppVersion);
	$DomAppVersion ->appendChild($AppVersionText);
	
	/*AppPlatform*/
	$DomAppPlatform =$dom ->createElement('AppPlatform');
	$App ->appendChild($DomAppPlatform);
	$AppPlatformText  = $dom->createTextNode($AppPlatform);
	$DomAppPlatform ->appendChild($AppPlatformText);
	
	
	/*AppIcon*/
	$DomAppIcon =$dom ->createElement('AppIcon');
	$App ->appendChild($DomAppIcon);
	$AppIconText  = $dom->createTextNode($AppIcon);
	$DomAppIcon ->appendChild($AppIconText);
	
	
	/*androidPath*/
	$DomAndroidPath =$dom ->createElement('androidPath');
	$App ->appendChild($DomAndroidPath);
	$AndroidPathText  = $dom->createTextNode('http://img.mobcent.com/d/aca/QRcCodeImg/android/app'.$AppcontentId.'/app'.$AppcontentId.'.png');
	$DomAndroidPath ->appendChild($AndroidPathText);
	
	/*IosPath*/
	$DomIosPath =$dom ->createElement('IosPath');
	$App ->appendChild($DomIosPath);
	$IosPathText  = $dom->createTextNode('http://img.mobcent.com/d/aca/QRcCodeImg/ios/app'.$AppcontentId.'/app'.$AppcontentId.'.png');
	$DomIosPath ->appendChild($IosPathText);
	
	$appxml = $dom->saveXML();
	file_put_contents('App.xml',$appxml);
	$data['rs']=1;
}catch(Expection $e)
{
	$data['rs']=0;
}
	echo echo_json($data);
?>