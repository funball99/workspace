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
require_once ('./abstractLogin.php');

class successImpl_x20 extends abstarctSuccess {
	public function getSuccessObj() {
		$base = new base();
		$_G['clientip'] = $ip = get_client_ip();
		$_ENV['user'] = new usermodel($base);
		$username =$_REQUEST['username'];
		$name_prem = uc_user_checkname ( $username );
		$user = $_ENV['user']->get_user_by_username($username);
		if($name_prem == -3){
			/*login*/
			$password = 'mobcent123';
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
			$User['secret'] = $accessSecret;
     		$User['token'] = $accessToken;
     		$User['role_num'] = $space['groupid'];
     		$User['uid'] = $user['uid'];
     		$User['fid'] =1;
     		$dataUserinfo['register']= false;
     		$dataUserinfo['rs']= 1;
     		$dataUserinfo['userInfo']= $User;
     		return $dataUserinfo;
		}else{
			/*regist*/
			$questionid = null;
			$answer = null;
			$email = '';
			$uid = uc_user_register ($username, $password, $email, $questionid, $answer, $ip );
			if ($uid <= 0 && $uid !=-1) {
				$obj ->rs = 0;
				$obj ->errcode = $registe_error_code;
				return $obj;
				exit ();
			}else if($uid ==-1)
			{
				$obj ->rs = 0;
				$obj ->errcode = '01020025';
				return $obj;
				exit ();
			}else {
				$profile = $verifyarr = array ();
				$emailstatus = 0;
				$initcredits = '0,0,0,0,0,0,0,0,0';
				$userdata = array (
						'uid' => $uid,
						'email' => $email,
						'username' => $username,
						'password' => $password,
						'groupid' => $groupid,
						'status' => '0',
						'credits' => explode ( ',', $initcredits ),
						'emailstatus' => $emailstatus
				);
				$status_data = array(
						'uid' => $uid,
						'regip' => (string)$ip,
						'lastip' => (string)$ip,
						'lastvisit' => TIMESTAMP,
						'lastactivity' => TIMESTAMP,
						'lastpost' => 0,
						'lastsendmail' => 0
				);
				$profile['uid'] = $uid;
				$field_forum['uid'] = $uid;
				$field_home['uid'] = $uid;
				DB::insert('common_member', $userdata);
				DB::insert('common_member_status', $status_data);
				DB::insert('common_member_profile', $profile);
				DB::insert('common_member_field_forum', $field_forum);
				DB::insert('common_member_field_home', $field_home);
				$info = new mobcentGetInfo();
				$space = $info->getUserInfo(intval($uid));
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
				DB::insert('common_session',$data);
				require_once libfile ( 'cache/userstats', 'function' );
				build_cache_userstats ();
				$accessToken = substr(md5($uid.'mobcent'),0,-3);
				$accessSecret = substr(md5($password.'mobcent'),0,-3);
					
				$rowsAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
				if(!empty($rowsAccess))
				{
					$accessToken =$rowsAccess['user_access_token'];
					$accessSecret =$rowsAccess['user_access_secret'];
				}
				else {
					$accessToken = substr(md5($uid.'mobcent'),0,-3);
					$accessSecret = substr(md5($password.'mobcent'),0,-3);
					$status = array(
							'user_access_id' => '',
							'user_access_token' => $accessToken,
							'user_access_secret' => $accessSecret,
							'user_id' => $uid,
							'create_time' => date('Y-m-d H:m:s'),
					);
					C::t('common_member')->inser_accessTopkent($accessToken,$accessSecret,$uid,date('Y-m-d H:m:s'));
				}
				$User['secret'] = $accessSecret;
				$User['token'] = $accessToken;
				$User['role_num'] = $space['groupid'];
				$User['uid'] = $uid;
				$User['fid'] =1;
				$dataUserinfo['register']= true;
				$dataUserinfo['rs']= 1;
				$dataUserinfo['userInfo']= $User;
				
				return $dataUserinfo;
			}
		}
	}

}

?>
