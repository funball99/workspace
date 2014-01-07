<?php
require_once '../../config/config_ucenter.php';
define('IN_MOBCENT',1);
require_once '../../source/class/class_core.php';
require_once '../../uc_client/client.php';
require_once '../../uc_client/model/base.php';
require_once '../../uc_client/model/user.php';
require_once '../tool/tool.php';
require_once '../model/table/x25/topic.php';
require_once DISCUZ_ROOT.'./uc_client/client.php';
require_once DISCUZ_ROOT.'./source/function/function_member.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once ('./abstractLogin.php');
require_once '../Config/public.php';

class loginImpl_x25 extends abstractLogin {	
	function login() {
		$ip = get_client_ip();
		$base = new base();
		$username = $_GET['email'] ?echo_urldecode($_GET['email']): '';
		$password = echo_urldecode($_GET['password']);
		
		$_G['clientip'] = $ip = get_client_ip();
		$loginfield = 'username';
		$questionid = $answer = '';
		$_G['loginsubmit']='yes';
		$_G['handlekey']='login';
		$_G['loginhash']='LKlwF';
		$_GET['action']='login';
		$_GET['mod']='logging';
		$_ENV['user'] = new usermodel($base);
		if($loginfield == 'uid') {
			$isuid = 1;
		} elseif($loginfield == 'email') {
			$isuid = 2;
		} elseif($loginfield == 'auto') {
			$isuid = 3;
		} else {
			$isuid = 0;
		}
		
 
		if($isuid == 1) {
			$user = $_ENV['user']->get_user_by_uid($username);
		} elseif($isuid == 2) {
			$user = $_ENV['user']->get_user_by_email($username);
		} else {
			$user = $_ENV['user']->get_user_by_username($username);
		}
		if($user['password']){
		
			$passwordmd5 = preg_match('/^\w{32}$/', $password) ? $password : md5($password);
			if(empty($user)) {
				$status = -1;
			} elseif($user['password'] != md5($passwordmd5.$user['salt'])) {
				$status = -2;
			} elseif($checkques && $user['secques'] != '' && $user['secques'] != $_ENV['user']->quescrypt($questionid, $answer)) {
				$status = -3;
			} else {
				$status = $user['uid'];
			}			 
		}else{
			$obj -> rs = 0;
			$obj -> errcode = '01010000';
			$obj -> username = $username;
			$obj -> password = $password;
			return $obj;			
			exit();
		}
 		
		
		if(empty($_POST['imei'])){
			$shebei=$_POST['imsi']; 	
		}else{
			$shebei=$_POST['imei'];
		}
		
		
		/*last login time*/
		$file_name2='lasttime/'.$shebei.'_last.txt';
		if(!file_exists($file_name2))
		{
			fopen("$file_name2", "w+");
			file_put_contents($file_name2, time());
		}
		$lasttime = file_get_contents($file_name2);
		
		
		$file_name='count/'.$shebei.'.txt'; 
		
		if(!file_exists($file_name))
		{
			fopen("$file_name", "w+"); 
			file_put_contents($file_name, '0');
		} 
		
		if(time()-$lasttime>300){
			file_put_contents($file_name2,time());
			file_put_contents($file_name, '0');
		}
		
		if($status > 0){
			require_once libfile('function/member');
			require_once '../public/mobcentDatabase.php';
			$info = new mobcentGetInfo();
			$space = $info->getUserInfo(intval($user['uid']));
			$a=1;
			setloginstatus($space, $_GET['cookietime'] ? 2592000 : 0);
			C::t('common_member_status')->update($_G['uid'], array('lastip' => $_G['clientip'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));
			
			$sid = Common::randomkeys(6);
			$ip_array = explode('.', $ip);
			$data = array(
					'sid' => $sid,
					'ip1' => $ip_array[0],
					'ip2' => $ip_array[1],
					'ip3' => $ip_array[2],
					'ip4' => $ip_array[3],
					'uid' => (Int)$space['uid'],
					'username' =>$space['username'],
					'groupid' => $space['groupid'],
					'invisible' =>'0',
					'action' => 'APPTYPEID' ,
					'lastactivity' => time(),
					'fid' => '0',
					'tid' => '0',
					'lastolupdate' => '0'
			);
			$status = DB::fetch_first ( "SELECT uid,invisible as status FROM ".DB::table('common_session')." WHERE uid=".$space['uid'] );
			if(empty($status))
			{
				DB::query('DELETE FROM '.DB::table('common_session'));
				DB::insert('common_session',$data);
			}else
			{
				DB::query('DELETE FROM '.DB::table('common_session').' WHERE uid='.$space['uid']);
				DB::insert('common_session',$data);
				
			}
			/*check accessToken isset*/
			$rowsAccess = $info->sel_accessTopkentByUid($user['uid']);			
			if(!empty($rowsAccess))
			{
				$accessToken =$rowsAccess['user_access_token'];
				$accessSecret =$rowsAccess['user_access_secret'];
			}else{
				$accessToken = substr(md5($user['uid'].'mobcent'),0,-3);
				$accessSecret = substr(md5($password.'mobcent'),0,-3);
					$status = array(
							'user_access_id' => '',
							'user_access_token' => $accessToken,
							'user_access_secret' => $accessSecret,
							'user_id' => $user['uid'],
							'create_time' => date('Y-m-d H:i:s'),
						 
					);
					$info->inser_accessTopkent($accessToken,$accessSecret,$user['uid'],date('Y-m-d H:i:s'));
					
					
			 }
			 $arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);  
			 
			 $xm=new topic();
			 $url=file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
			 $result =$xm->xml_to_array($url);
			 
			 if(!empty($result)){
			 	foreach($result['login'] as $rs){
			 		$ms= $rs['0'];
			 	}
			 	if(count($result)==0){
			 		$ms=5; /*if App.xml is not exist,run this code.*/
			 	}
			 	$cn = file_get_contents($file_name);
			 	$cn++;
			 	file_put_contents($file_name, $cn);
			 		
			 	if($cn>$ms){
			 		$obj -> rs = 0;
			 		$obj -> errcode = '03000001';
			 		$obj -> counts = $cn;
			 		return $obj;
			 		exit();
			 	}else{
			 		$obj -> secret = $accessSecret;
			 		$obj -> token = $accessToken;
			 		$obj -> uid = (Int)$user['uid'];
			 		$obj -> avatar = userIconImg($user['uid']);
			 		$obj -> fid = 1;
			 		$obj -> rs = 1;
			 		$obj -> counts = $cn;
			 		return $obj;
			 	}
			 }else{
			 	$obj -> secret = $accessSecret;
			 	$obj -> token = $accessToken;
			 	$obj -> uid = (Int)$user['uid'];
			 	$obj -> avatar = userIconImg($user['uid']);
			 	$obj -> fid = 1;
			 	$obj -> rs = (Int)1;
			 	$obj -> counts = $cn;
			 	return $obj;
			 }
			
			}else{
				$obj -> rs = (Int)0;
				$obj -> errcode = '01010000';
				return $obj;			
				exit();
			}
			 
			
		}
}
 
 
?>