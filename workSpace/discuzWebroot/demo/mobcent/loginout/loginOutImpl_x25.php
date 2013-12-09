<?php
define('UC_API', true);
define('IN_MOBCENT',1);
require_once '../../source/class/class_core.php';
require_once '../../uc_client/client.php';
require_once '../../uc_client/model/base.php';
require_once '../../uc_client/model/user.php';
require_once '../tool/tool.php';
require_once '../../config/config_ucenter.php';
require_once DISCUZ_ROOT.'./uc_client/client.php';
require_once DISCUZ_ROOT.'./source/class/class_member.php';
require_once DISCUZ_ROOT.'./source/function/function_member.php';
require_once '../tool/constants.php';
C::app ()->init ();
require_once ('./abstractLoginOut.php');
class loginOutImpl_x25 extends abstractLoginOut {
	
	function getloginOutObj() {
		clearcookies();
		$_G['groupid'] = $_G['member']['groupid'] = 7;
		$_G['uid'] = $_G['member']['uid'] = 0;
		$_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';
		if(empty($_G['uid']) && empty($_G['username']))
		{
			require_once '../public/mobcentDatabase.php';
			$info = new mobcentGetInfo();
			$accessSecret = $_GET['accessSecret'];
			$accessToken = $_GET['accessToken'];
			$uid = $info->sel_accessTopkent($accessSecret, $accessToken);
			DB::query('DELETE FROM '.DB::table('common_session').' WHERE uid='.$uid['user_id']);
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