<?php
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../tool/constants.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once ('./abstractUpdateUserInfo.php');
class updateUserInfoImpl_x25 extends abstractUpdateUserInfo {
	function updateUserInfo() {
		try{
			$info = new mobcentGetInfo();
			$accessSecret = $_GET['accessSecret'];
			$accessToken = $_GET['accessToken'];
			$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
			$group = $info->rank_check_allow($accessSecret,$accessToken,$qquser);
			if(!$group['allowvisit'])
			{
				$data_post['rs'] = 0;
				$data_post['errcode'] = '01110001';
				return $data_post;
				exit();
			}
			$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
			$uid = $_G ['uid'] = $arrAccess['user_id'];
			if(empty($uid))
			{
				return $info -> userAccessError();
				exit();
			}
			$space = getuserbyuid ( $_G ['uid'] );
			@include_once DISCUZ_ROOT . './data/cache/cache_domain.php';
			include_once libfile ( 'function/profile' );
			$setarr ['gender'] = intval ( $_GET ['gender'] );
			if ($setarr) {
				$res = C::t ( 'common_member_profile' )->update ( $uid, $setarr );
			}
			
			manyoulog ( 'user', $uid, 'update' );
			$operation = 'gender';
			include_once libfile ( 'function/feed' );
			feed_add ( 'profile', 'feed_profile_update_' . $operation, array (
			'hash_data' => 'profile'
			) );
			countprofileprogress ();
			
			$avatar_path = '../../uc_server/data/avatar/cache/' . $this->get_avatar_path ( $uid );
			$avatar_file = $this->get_avatar_file ( $uid );
			
			$save_path = '../../uc_server/data/avatar/' . $this->get_avatar_path ( $uid );
			
			if (! is_dir ( $save_path )) {
				mkdir ( $save_path, 0777, true );
			}
			foreach ( $avatar_file as $k => $new_file_name ) {
				if (!is_file ( $avatar_path . $new_file_name )) {
					$arr[] = '';
				}
				else
				{
					$arr[] = 'true';
				}
			}
			$arr = array_filter($arr);
			if(!empty($arr))
			{
				foreach ( $avatar_file as $k => $new_file_name ) {
					if (is_file ( $save_path . $new_file_name )) {
						unlink ( $save_path . $new_file_name );
					}
				}
				$i = 1;
				foreach ( $avatar_file as $k => $new_file_name ) {
					if (is_file ( $avatar_path . $new_file_name )) {
						if (copy ( $avatar_path . $new_file_name, $save_path . $new_file_name )) {
							$res = true;
							$i ++;
						} else {
							$res = false;
							$jsonobj -> rs = FAILED;
							$jsonobj -> errcode = "01300000";
							return $jsonobj;
						}
					}
				}
			}
			
			
			$jsonobj -> rs = SUCCESS;
			return $jsonobj;
		}catch(Exception $e){
			$obj -> rs = 0;
			$obj -> errcode = "01000000";
			return $obj;
		}
		
		}
		function get_avatar_path($uid) {
			$uid = abs ( intval ( $uid ) );
			$uid = sprintf ( "%09d", $uid );
			$dir1 = substr ( $uid, 0, 3 );
			$dir2 = substr ( $uid, 3, 2 );
			$dir3 = substr ( $uid, 5, 2 );
			return $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
		}
		function get_avatar_file($uid, $type = '') {
			$uid = abs ( intval ( $uid ) );
			$uid = sprintf ( "%09d", $uid );
			$dir1 = substr ( $uid, 0, 3 );
			$dir2 = substr ( $uid, 3, 2 );
			$dir3 = substr ( $uid, 5, 2 );
			$typeadd = $type == 'real' ? '_real' : '';
			return array (
					substr ( $uid, - 2 ) . $typeadd . "_avatar_big.jpg",
					substr ( $uid, - 2 ) . $typeadd . "_avatar_middle.jpg",
					substr ( $uid, - 2 ) . $typeadd . "_avatar_small.jpg"
			);
		}
}

?>