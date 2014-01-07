<?php
require_once './abstractBlack.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../public/mobcentDatabase.php';
define('IN_MOBCENT',1);
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile('function/friend');

class postBlackImpl_x25 extends abstractBlack {
	public function getBlackObj() {
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
		$_G['uid'] = $uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		$buid = intval($_GET['bUserId']);
		if($_GET['state']==0){
			$return=C::t('home_blacklist')->delete_by_uid_buid($uid, $buid);
		
		}elseif($_GET['state']==1){
			friend_delete($buid);
			$return = C::t('home_blacklist')->insert(array('uid'=>$uid, 'buid'=>$buid, 'dateline'=>time()), true, true, true);
		
		}
		if($return){
			$data_notice['rs'] = (Int)1;
		}else{
			$data_notice['rs'] = (Int)0;
			$data_notice['errcode'] = '03100010';
		}
		return $data_notice;
			}
}

?>