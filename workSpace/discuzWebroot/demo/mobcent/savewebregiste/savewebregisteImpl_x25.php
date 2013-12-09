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
require_once ('./abstractSaveWebRegiste.php');
require_once '../public/mobcentDatabase.php';
require_once '../tool/Thumbnail.php';
class savewebregisteImpl_x25 extends abstractSaveWebRegiste {
	function savewebregiste(){	
		$groupid = 10;
		$questionid = null;
		$answer = null;
		if(UC_DBCHARSET == 'utf8'){
			$username = $_GET ['user'] ? $_GET ['user'] : '';
		}else{
			$username = $_GET ['user'] ? mb_convert_encoding($_GET ['user'], 'GBK' , 'UTF-8') : '';
 		}
		$webUsername = $_GET ['webuser'] ? echo_urldecode($_GET ['webuser']) : '';
		$webToken = $_GET ['webtoken'] ? echo_urldecode($_GET ['webtoken']) : '';
		$webuid = $_GET ['webuid'] ? echo_urldecode($_GET ['webuid']) : '';
		$wbIcon = $_GET ['wbIcon'] ? echo_urldecode($_GET ['wbIcon']) : '';
		$email_salt = substr(uniqid(rand()), -6);
		$email ='web'.$email_salt.'_mobcent@mobcent.com';
		$password = 'mobcent123'.$email_salt;
		
		$name_prem = uc_user_checkname ( $username );
		$ip = get_client_ip();
			$questionid = null;
			$answer = null;
			$_G ['clientip'] = $ip = get_client_ip ();
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
				$webdata = array(
						'uid' => $uid,
						'username' => $username,
						'sina_uid' => $webuid,
						'sina_username' => $webUsername,
						'token' => $webToken,
						'remind_in' => 1,
						'expires_in' =>1,
						'thread' =>1,
						'reply' =>1,
						'follow' => 1 ,
						'blog' => 1,
						'doing' => 1,
						'share' => 1,
						'article' => 1,
						'dateline' => time(),
						'update' => time()
				);
				
				//$sn=serialize($webdata);
				//file_put_contents('sn.txt', $sn);
				
				DB::insert('home_weibo',$webdata);
				$info = new mobcentGetInfo();
				$space = $info->getUserInfo(intval($uid));
				$avatar_path = dirname ( __FILE__ ) . '/../../uc_server/data/avatar/' .get_avatar_path ( $uid );
				$avatar_file = get_avatar_file ( $uid );
				if (file_exists( $avatar_path )) {
					mkdir ( $avatar_path, 0777, true );
					
						
					$data = file_get_contents($wbIcon);
					foreach ( $avatar_file as $k => $new_file_name ) {
						$file = $avatar_path . $new_file_name;
						if(!file_exists($file)){
							
							file_put_contents($new_file_name.'.txt', file_exists( $file ).'@'.$wbIcon);
								
							if (strlen ( $data ) > 2) {
								if (is_file ( $file ))
									unlink ( $file );
								$handle = fopen ( $file, 'w+' );
								fwrite ( $handle, $data );
								fclose ( $handle );
							}
							$res = new Thumbnail($file,$new_file_name);
							$fiel_name_array = explode ( '_', $new_file_name );
							$img_name_type = end ( $fiel_name_array );
							switch ($img_name_type) {
								case "big.jpg" :
									$res -> zoomcutPic($file, $avatar_path, $new_file_name, 150);
									if ($res) {
										$res = true;
									} else {
										$res = false;
									}
										
									break;
								case "middle.jpg" :
									$res -> zoomcutPic($file, $avatar_path, $new_file_name, 120);
									if ($res) {
										$res = true;
									} else {
										$res = false;
									}
									break;
								case "small.jpg" :
									$res -> zoomcutPic($file, $avatar_path, $new_file_name, 48);
									if ($res) {
										$res = true;
									} else {
										$res = false;
									}
									break;
							}
						}
					
					}
				}
				
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

?>