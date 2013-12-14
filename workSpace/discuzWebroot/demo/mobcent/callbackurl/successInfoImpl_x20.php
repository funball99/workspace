<?php
require_once ('./abstarctSuccess.php');
define('IN_MOBCENT',1);
define('IN_UC', true);

require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../../source/function/function_forumlist.php';
require_once '../tool/tool.php';
require_once '../tool/Thumbnail.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);



class successInfoImpl_x20 extends abstarctSuccess {
	public function getSuccessObj($token,$userArr) {
		global $_G;
		$serverUrl =str_replace('/mobcent/callbackurl/', '', $_G['siteurl']);
		$discuz = & discuz_core::instance();
		$discuz->init();
		require_once DISCUZ_ROOT.'./uc_client/client.php';
		require_once '../../uc_client/model/base.php';
		require_once '../../uc_client/model/user.php';
	
		$base = new base();
		$_G['clientip'] = $ip = get_client_ip();
		$_ENV['user'] = new usermodel($base);
		if(UC_DBCHARSET == 'utf8'){
			$username= $userArr->screen_name;
		}else{
			$username = mb_convert_encoding($userArr->screen_name, 'GBK' , 'UTF-8');
		}
		$Icon =$userArr->profile_image_url;
		$user = DB::fetch_first("SELECT uid FROM ".DB::table('home_weibo')." WHERE sina_uid ='".$userArr->id."' and token ='".$token."'");
		if(isset($user) && !empty($user)){
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
     		$User['icon'] =$Icon;
     		$dataUserinfo['register']= false;
     		$dataUserinfo['rs']= 1;
     		$dataUserinfo['userInfo']= $User;
     		$Surl =$serverUrl.'/mobcent/callbackurl/success.php?'.$this->transfer($dataUserinfo);
     						header('location:'.$Surl);
		}else{
			/*regist*/
			$User['secret'] = 1;
	     		$User['token'] = $token;
	     		$User['role_num'] = 1;
	     		$User['uid'] = $userArr->id;
	     		$User['fid'] =(int)1;
	     		
	     		$dataUserinfo['register']= true;
	     		$dataUserinfo['rs']= 1;
	     		$dataUserinfo['userInfo']= $User;
	     		$UserBean['address'] = '';
	     		$UserBean['email'] = $email;
	     		$UserBean['role_num'] = 1;
	     		$UserBean['gender'] = (int)$userArr->gender;
	     		$UserBean['msn'] ='';
	     		$UserBean['icon'] =$Icon;
	     		$UserBean['nickname'] =$username;
	     		$UserBean['password'] =$password;
	     		$UserBean['phone'] ='';
	     		$UserBean['qq'] ='';
	     		$UserBean['uid'] = $userArr->id;
	     		$UserBean['zipCode'] ='';
	     		
	     		$dataUserinfo['userBean']= $UserBean;
				$Surl =$serverUrl.'/mobcent/callbackurl/success.php?'.$this->transfer($dataUserinfo);
     						header('location:'.$Surl);
		}
	}
}

?>