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
$discuz = & discuz_core::instance();
$discuz->init();
require_once DISCUZ_ROOT.'./uc_client/client.php';
require_once '../../uc_client/model/base.php';
require_once '../../uc_client/model/user.php';
require_once ('./abstractLoginOut.php');

class loginOutImpl_x20 extends abstractLoginOut {
	function getloginOutObj() {
		$_G['groupid'] = $_G['member']['groupid'] = 7;
		$_G['uid'] = $_G['member']['uid'] = 0;
		$_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';
		if(empty($_G['uid']) && empty($_G['username']))
		{
			$accessSecret = $_GET['accessSecret'];
			$accessToken = $_GET['accessToken'];
			$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
			$_G['uid'] = $uid = $arrAccess['user_id'];
			if(empty($uid))
			{
				return C::t('common_member') -> userAccessError();
				exit();
			}
			DB::query('DELETE FROM '.DB::table('common_session').' WHERE uid='.$uid);
			$data['rs'] = (Int)1;
		
		}
		else
		{
			$data['rs'] = (Int)0;
		}
		return $data;
		}

}

?>