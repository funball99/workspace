<?php
require_once './abstractUnFollowUser.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once '../public/mobcentDatabase.php';

class unFollowUserImpl_x25 extends abstractUnFollowUser {
	function getunFollowUserObj() {
		require_once '../public/mobcentDatabase.php';
		$info = new mobcentGetInfo ();
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
			$uid = $arrAccess['user_id'];
			if(empty($uid))
			{
				return $info -> userAccessError();
				exit();
			}
		$delfollowuid = intval ( $_GET ['followId'] );
		if (empty ( $delfollowuid )) {
			echo '{"rs":0,"errcode":"01000000"}';
			exit ();
		}
		$affectedrows = C::t ( 'home_follow' )->delete_by_uid_followuid ( $uid, $delfollowuid );
		if ($affectedrows) {
			C::t ( 'home_follow' )->update_by_uid_followuid ( $delfollowuid, $uid, array (
			'mutual' => 0
			) );
			C::t ( 'common_member_count' )->increase ( $uid, array (
			'following' => - 1
			) );
			C::t ( 'common_member_count' )->increase ( $delfollowuid, array (
			'follower' => - 1,
			'newfollower' => - 1
			) );
		}
		$data['rs'] = 1;
		return $data;
		}

}

?>