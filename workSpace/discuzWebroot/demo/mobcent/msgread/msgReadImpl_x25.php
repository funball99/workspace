<?php
require_once './abstarctMsgRead.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../model/table/x25/table_ucenter_pm_members.php';
require_once '../tool/constants.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();

class msgReadImpl_x25 extends abstarctMsgRead {
	public function getMsgReadObj() {
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
		$uid = $_G['uid'] = $arrAccess['user_id'];
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		$_GET['do'] = 'pm';
		$_GET['subop'] = $_GET['subop'] ;
		$isnew = 1;
		$list = array();
		$plid = empty($_GET['msg_id'])?0:intval($_GET['msg_id']);
		
		if($plid) {
			$res = table_ucenter_pm_members::isreadstatus($uid,$plid);
		}
		$data['rs'] =1;
		return $data;
		}
}

?>