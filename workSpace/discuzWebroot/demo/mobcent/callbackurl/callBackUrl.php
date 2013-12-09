<?php 
require_once ('../../source/class/class_core.php');
require_once ('../../source/discuz_version.php');
define ( 'ROOT_PATH', dirname ( __FILE__ ) . '/../' );
define ( 'CONFIG', '../config/config_global.php' );
require_once '../tool/tool.php';
if (DISCUZ_VERSION == 'X2'){
	$s=file_exists('../manage/App.xml')?join("",file('../manage/App.xml')):array();
}else{
	$s=file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
}
$result =xml_to_array($s);
$code =$_REQUEST['code'];

$appId = $result['wbkey'][0][0];
$appKey =$result['secret'][0][0];
global $_G;
$serverUrl =str_replace('/mobcent/callbackurl/', '', $_G['siteurl']);

 $tokenUrl = 'https://api.weibo.com/oauth2/access_token?client_id='.$appId.'&client_secret='.$appKey.'&grant_type=authorization_code&redirect_uri='.$serverUrl.'/mobcent/callbackurl/callBackUrl.php&code='.$code;
 
 $post_data = array();
 $post_data['client_id'] = $appId;
 $post_data['client_secret'] =$appKey;
 $post_data['submit'] = "submit";
 $post_data['grant_type'] = "authorization_code";
 $post_data['redirect_uri'] = $serverUrl.'/mobcent/callbackurl/callBackUrl.php';
 $post_data['code'] = $code;
 $url='https://api.weibo.com/oauth2/access_token';
 $o="";
 
 foreach ($post_data as $k=>$v)
 {
 	$o.= "$k=".urlencode($v)."&";
 }
 $post_data=substr($o,0,-1);
 $result = getWeiboData($url,$post_data);
 $parm = $parmToken =array();
 $parm = json_decode($result);

     if (isset($parm->error))
     {
        $Furl =$serverUrl.'/mobcent/callbackurl/fail.php?'.'{rs:0}';
     	header('location:'.$Furl);
     }else
     {
     	$userinfo ='https://api.weibo.com/2/users/show.json?access_token='.$parm->access_token.'&oauth_consumer_key='.$appId.'&uid='.$parm->uid;
     	/*$string = file_get_contents($userinfo);*/
     	$ch = curl_init();
     	curl_setopt($ch, CURLOPT_URL,$userinfo);
     	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
     	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1) ;
     	$string= curl_exec($ch);
     	//file_put_contents('string.txt', $string);
     	
     	$userInfo = mb_convert_encoding( $string, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
     	$userArr = json_decode($userInfo); 
     	if(UC_DBCHARSET == 'utf8'){
     		$username= $userArr->screen_name;
     	}else{
     		$username = mb_convert_encoding($userArr->screen_name, 'GBK' , 'UTF-8');
     	}
     	$userIcon = $userArr->profile_image_url;
     	$gender = $userArr->gender;
     	$version = 'x25';
     	if (DISCUZ_VERSION == 'X2')
     		$version = 'x20';
     	
     	require_once './successInfoImpl' . '_' .  $version.'.php';
     	$className = 'successInfoImpl' . '_' .  $version;
     	
     	
     	$class=new ReflectionClass($className);
		$obj = $class->newInstance();
		
		$retObj = $obj->getSuccessObj($parm->access_token,$userArr);
     	$Surl =$serverUrl.'/mobcent/callbackurl/success.php?'.$obj->transfer($retObj);
     	header('location:'.$Surl);
     }
function getWeiboData($url,$post_data){
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$result = curl_exec($ch);
	if($result === false){
		echo curl_error($ch);
	}
	return $result;
}
?>