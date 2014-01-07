<?php
/*login*/
require_once ('../../source/class/class_core.php');
require_once ('../../source/discuz_version.php');
require_once '../tool/tool.php';
/*key url*/
define ( 'ROOT_PATH', dirname ( __FILE__ ) . '/../' );
define ( 'CONFIG', '../config/config_global.php' );
if (DISCUZ_VERSION == 'X2'){
	$s=file_exists('../manage/App.xml')?join("",file('../manage/App.xml')):array();
}else{
	$s=file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
}

$result =xml_to_array($s);
$platformId =$_GET['platformId'];
/*$appId = '2692596781';*/
$appId = $result['wbkey'][0][0];
$appKey =$result['secret'][0][0];
$state ='test';
global $_G;
$serverUrl =str_replace('/mobcent/weblogin/', '', $_G['siteurl']);
switch($platformId)
{
	case 20:
		header('location:https://graph.qq.com/oauth2.0/authorize?client_id='.$appId.'&response_type=code&redirect_uri='.$serverUrl.'/mobcent/callbackurl/callBackUrl.php&scope=get_user_info,add_topic&state='.$state);
		break;
	case 1:
		header('location:https://api.weibo.com/oauth2/authorize?client_id='.$appId.'&response_type=code&redirect_uri='.$serverUrl.'/mobcent/callbackurl/callBackUrl.php&display=mobile');
		break;
}

?>