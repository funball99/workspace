<?php
define ( 'UC_API', true );
define ( 'UC_CONNECT', 'mysql' );
define('NOROBOT', TRUE);
require_once '../../source/class/class_core.php';
require_once '../../uc_client/client.php';
require_once '../../uc_client/model/base.php';
require_once DISCUZ_ROOT . './config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../model/table/x25/topic.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once ('./abstractRegiste.php');
require_once '../public/mobcentDatabase.php';
class registeImpl_x25 extends abstractRegiste {
	function registe(){		
		if(empty($_POST['imei'])){
			$shebei=$_POST['imsi'];
		}else{
			$shebei=$_POST['imei'];
		}
		
		/*last register time*/
		$file_name2='lasttime/'.$shebei.'_last.txt';
		if(!file_exists($file_name2))
		{
			fopen("$file_name2", "w+");
			file_put_contents($file_name2, time());
		}
		$lasttime = file_get_contents($file_name2);
		
		/*regcount*/
		$file_name='regcount/'.$shebei.'.txt';		
		if(!file_exists($file_name))
		{
			fopen("$file_name", "w+");
			file_put_contents($file_name, '0');
		}		
		
		if(time()-$lasttime>300){
			file_put_contents($file_name2,time());
			file_put_contents($file_name, '0');
		}
		
		$cn = file_get_contents($file_name);
		$cn++;
		file_put_contents($file_name, $cn);
				
		$xm=new topic();
		$url=file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
		$result =$xm->xml_to_array($url);			
		foreach($result['register'] as $rs){
			$ms= $rs['0'];
		}
		if(count($result)==0){
			$ms=5; /*if App.xml is not exist,run this code.*/
		}
		if($cn>$ms){
			$obj -> rs = 0;
			$obj -> errcode = '03000003';
			return $obj;
			exit();
		}else{
		
			$ip = get_client_ip();
			$questionid = null;
			$answer = null;
			$_G ['clientip'] = $ip = get_client_ip ();
			
			
			global $_G;
			if($_G['setting']['regverify']) {
				$groupid = 8;
			} else {
				$groupid = $_G['setting']['newusergroupid'];
			}
			$username = $_GET ['user'] ? echo_urldecode($_GET ['user']) : '';
			$password = $_GET ['password'] ? echo_urldecode($_GET ['password']) : '';
			$email = $_GET ['email'] ? echo_urldecode($_GET ['email']) : '';
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
			$uid = uc_user_register ( addslashes ( $username ), $password, $email, $questionid, $answer, $ip );
			if ($uid <= 0 && $uid !=-1) {
				$obj ->rs = 0;
				$obj ->errcode = $registe_error_code;
				return $obj;
				exit ();  
			} 
			else if($uid ==-1)
			{
				$obj ->rs = 0;
				$obj ->errcode = '01020025';
				return $obj;
				exit ();
			}else {					
				$profile = $verifyarr = array ();
				$emailstatus = 0;
				$initcredits = '0,0,0,0,0,0,0,0,0';
				$init_arr = array (
						'credits' => explode ( ',', $initcredits ),
						'profile' => $profile,
						'emailstatus' => $emailstatus
				);
			
				$res = C::t ( 'common_member' )->insert ( $uid, $username, $password, $email, $ip, $groupid, $init_arr );
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
				DB::query('DELETE FROM '.DB::table('common_session'));
				DB::insert('common_session',$data);
				require_once libfile ( 'cache/userstats', 'function' );
				build_cache_userstats ();
				
				$accessToken = substr(md5($uid.'mobcent'),0,-3);
				$accessSecret = substr(md5($password.'mobcent'),0,-3); 
				$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
				if(empty($arrAccess['user_id']))
				{
					$status = array(
							'user_access_id' => '',
							'user_access_token' => $accessToken,
							'user_access_secret' => $accessSecret,
							'user_id' => $uid,
							'create_time' => date('Y-m-d H:i:s'),
					);
					$info->inser_accessTopkent($accessToken,$accessSecret,$uid,date('Y-m-d H:i:s'));
				}
				$obj -> secret = $accessSecret;
				$obj -> token = $accessToken;
				$obj -> uid = $uid;
				$obj -> fid = 1;
				$obj ->rs =1;
				return $obj;
			}
		}
	}
}

?>