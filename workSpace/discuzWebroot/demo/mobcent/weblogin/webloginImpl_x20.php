<?php
define('IN_MOBCENT',1);
define('IN_UC', true);
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../../source/function/function_forumlist.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once DISCUZ_ROOT.'./uc_client/client.php';
require_once '../../uc_client/model/base.php';
require_once '../../uc_client/model/user.php';
require_once ('./abstractWebLogin.php');
class webloginImpl_x20 extends abstractWebLogin {
	function login() {
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
		
		if($status > 0){
			require_once libfile('function/member');
			require_once '../public/mobcentDatabase.php';
			$info = new mobcentGetInfo();
			$space = $info->getUserInfo(intval($user['uid']));
			$result['member'] = DB::fetch_first("SELECT * FROM ".DB::table('common_member')." WHERE uid=".$user['uid']);
			$a=1;
			setloginstatus($result['member'], $_GET['cookietime'] ? 2592000 : 0);
			$sid = Common::randomkeys(6);
			$ip_array = explode('.', $ip);
			$data = array(
					'sid' => $sid,
					'ip1' => $ip_array[0],
					'ip2' => $ip_array[1],
					'ip3' => $ip_array[2],
					'ip4' => $ip_array[3],
					'uid' => $space['uid'],
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
				DB::insert('common_session',$data);
			}else
			{
				DB::query('DELETE FROM '.DB::table('common_session').' WHERE uid='.$space['uid']);
				DB::insert('common_session',$data);
				
			}
			
			DB::query("UPDATE ".DB::table('common_member_status')." SET lastip='".$ip."', lastvisit='".time()."', lastactivity='".TIMESTAMP."' WHERE uid='".$space['uid']."'");
     		$home_member = new table_common_member();
     		$recommend = DB::fetch_first("SELECT COUNT(*) AS num FROM ".DB::table('home_specialuser')." WHERE uid = ".$user['uid']);
     		if($recommend['num']){
	     		DB::query("UPDATE ".DB::table('home_specialuser')." SET status = 1, dateline='".TIMESTAMP."' WHERE uid='".$user['uid']."'"); 
     		}
     		
     		$rowsAccess = C::t('common_member')->sel_accessTopkentByUid($user['uid']);
     		if(!empty($rowsAccess))
     		{
     			$accessToken =$rowsAccess['user_access_token'];
     			$accessSecret =$rowsAccess['user_access_secret'];
     		}
     		else {
     			$accessToken = substr(md5($user['uid'].'mobcent'),0,-3);
     			$accessSecret = substr(md5($password.'mobcent'),0,-3);
     			$status = array(
     					'user_access_id' => '',
     					'user_access_token' => $accessToken,
     					'user_access_secret' => $accessSecret,
     					'user_id' => $user['uid'],
     					'create_time' => date('Y-m-d H:m:s'),
     			);
     			C::t('common_member')->inser_accessTopkent($accessToken,$accessSecret,$user['uid'],date('Y-m-d H:m:s'));
     		}
			$obj -> secret = $accessSecret;
			$obj -> token = $accessToken;
			$obj -> uid = $user['uid'];
			$obj -> fid = 1;
			$obj -> rs = 1;
			return $obj;
		}else{
			$obj -> rs = 0;
			$obj -> errcode = '01010000';
			return $obj;
			exit();
		}
		
		}
		

}

?>