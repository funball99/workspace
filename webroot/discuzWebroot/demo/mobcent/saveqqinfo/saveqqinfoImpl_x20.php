<?php
define ( 'UC_API', true );
define ( 'IN_UC', true );
define ( 'UC_CONNECT', 'mysql' );
//require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../source/function/function_forum.php';
require_once '../../uc_client/model/base.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../model/table/x20/topic.php';
require_once '../Config/public.php';
require '../../uc_client/client.php';
require_once ('./abstractSaveqqinfo.php');
require_once '../public/mobcentDatabase.php';
require_once '../tool/Thumbnail.php';
class saveqqinfoImpl_x20 extends abstractSaveqqinfo {
	function saveqqInfo() {
		global $_G;
		if($_G['setting']['regverify']) {
			$groupid = 8;
		} else {
			$groupid = $_G['setting']['newusergroupid'];
		}
		$questionid = null;
		$answer = null;
		$username = $_GET['user']?echo_urldecode($_GET['user']):'';
		$openId = $_GET['openId'];
		$oauthToken = $_GET['oauth_token'];
		$platformId = $_GET['platformId'];
		$sex = $_GET['gender'];
		$email_salt = substr(uniqid(rand()), -6);
		//$email ='web'.$email_salt.'_mobcent@mobcent.com';
		$username = $_GET['email']?echo_urldecode($_GET['email']):'';
		$password = 'mobcent123'.$email_salt;
		$ip = get_client_ip();
		$_G ['clientip'] = $ip = get_client_ip ();

		$name_prem = uc_user_checkname ( $username );
		$registe_error_code = '01020005';
		if ($name_prem == - 3) {
			$obj ->rs = 0;
			$obj ->errcode = $registe_error_code;
			return $obj;
			exit ();
		}
		$email_prem = uc_user_checkemail ( $email );
		if ($email_prem == - 3) {
			$obj ->rs = 0;
			$obj ->errcode = $registe_error_code;
			return $obj;
			exit ();
		}
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
			$qqbind = array(
					'mblid'=>'',
					'uid'  =>$uid,
					'uin'  =>$openId,
					'type' =>1,
					'dateline'=>time()
			);
			/* $result = DB::fetch(DB::query("SELECT uid FROM " .DB::table('common_connect_guest'). " WHERE conopenid='".$openId."' "));
			if(isset($result) && !empty($result))
			{
				DB::query("DELETE FROM " .DB::table('common_connect_guest'). " WHERE conopenid='".$openId." ");
			} */
			DB::insert('connect_memberbindlog',$qqbind);
			$qqdata = array(
							'uid' => $uid,
							'conuin' =>$oauthToken,
							'conuinsecret' =>'',
							'conopenid' => $openId,
							'conisfeed' => 1,
							'conispublishfeed' => 1,
							'conispublisht' =>1,
							'conisregister' =>1,
							'conisqzoneavatar' =>1,
							'conisqqshow' => 1 ,
					);
			DB::insert('common_member_connect',$qqdata);
			DB::query("UPDATE " .DB::table('common_member'). " SET avatarstatus=1,conisbind=1 WHERE uid=".$uid );
			DB::query("UPDATE " .DB::table('common_member_profile'). " SET gender=".$sex." WHERE uid=".$uid);
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
			$obj -> secret = $accessSecret;
			$obj -> token = $accessToken;
			$obj -> uid = $uid;
			$obj -> fid = 1;
			$obj ->rs =1;
		}
		return $obj;
	}
}

?>