<?php
require_once './abstractUnFollowUser.php';
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../public/common_json.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../uc_client/client.php';
require_once '../model/table/x20/mobcentDatabase.php';
class unFollowUserImpl_x20 extends abstractUnFollowUser {
	function getunFollowUserObj() {
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
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		$delfollowuid = intval ( $_GET ['followId'] );
		if (empty ( $delfollowuid )) {
			echo '{"rs":0,"errcode":"01000000"}';
			exit ();
		}
		$affectedrows = C::t ('home_follow')->delete_by_uid_followuid ( $uid, $delfollowuid );
		$data_notice['rs'] = 1;
		return $data_notice;
		}

}

?>