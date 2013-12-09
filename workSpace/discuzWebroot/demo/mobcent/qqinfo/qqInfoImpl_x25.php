<?php
require_once './abstractInfo.php';
define ( 'UC_API', true );
define ( 'UC_CONNECT', 'mysql' );
define('NOROBOT', TRUE);
require_once '../../source/class/class_core.php';
require_once '../../uc_client/client.php';
require_once '../../uc_client/model/user.php';
require_once '../../uc_client/model/base.php';
require_once DISCUZ_ROOT . './config/config_ucenter.php';
require_once DISCUZ_ROOT.'./source/function/function_member.php';
require_once '../tool/tool.php';
require_once '../tool/Thumbnail.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once '../public/mobcentDatabase.php';
class qqInfoImpl_x25 extends abstractInfo {
	public function getInfoObj() {
		$info = new mobcentGetInfo();
		$password = 'mobcent123';
		$_G['clientip'] = $ip = get_client_ip();
		$base = new base();
		$_ENV['user'] = new usermodel($base);
		$openId = $_GET['openId'];
		$oauthToken = $_GET['oauth_token'];
		$platformId = $_GET['platformId'];
		switch($platformId)
		{
			case 20:
			$user = $info->getqqUid($openId);
			$sql='';
			if(isset($user) && !empty($user))
			{
				$space = $info->getUserInfo(intval($user['uid']));
				$a=1;
				setloginstatus($space, $_GET['cookietime'] ? 2592000 : 0);
				C::t('common_member_status')->update($user['uid'], array('lastip' => $_G['clientip'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));
			
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
				/*check accessToken isset*/
				$rowsAccess = $info->sel_accessTopkentByUid($user['uid']);
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
					$info->inser_accessTopkent($accessToken,$accessSecret,$user['uid'],date('Y-m-d H:m:s'));
				}
			
				$User['secret'] = $accessSecret;
				$User['token'] = $accessToken;
				$User['role_num'] = $space['groupid'];
				$User['uid'] = (int)$user['uid'];
				$User['fid'] =(int)1;
				$User['icon'] ='';
				$dataUserinfo['register']= false;
				$dataUserinfo['rs']=(int)1;
				$dataUserinfo['userInfo']= $User;
			}
			else
			{
				$dataUserinfo['register']= true;
				$dataUserinfo['rs']= 1;
				$dataUserinfo['openId']=$openId;
				$dataUserinfo['oauth_token']=$oauthToken;
				$dataUserinfo['platformId']=$platformId;
			}
			break;
			case 1:
				break;
		}
		return $dataUserinfo;
	}
}
?>