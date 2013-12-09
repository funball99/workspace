<?php
require_once '../model/class_core.php';
require_once ('./abstractUpdataRead.php');
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';

require_once '../tool/tool.php';
require_once '../model/table/x20/table_home_notification.php';
require_once '../tool/constants.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../uc_client/client.php';
require_once '../model/table/x20/mobcentDatabase.php';

class updataReadImpl_x20 extends abstractUpdataRead {
	public function getUpdataReadObj() {
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
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $_G ['uid'] = $arrAccess['user_id'];
		if(empty($uid))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		$gpmids = trim ( $_GET ['gpmids'] );
		$plids = trim ( $_GET ['relpyRemindIds'] );  
		$type = $_GET ['type'] ? trim ( $_GET ['type'] ) : 'at';
		if ($gpmids) {
			$gpmidarr = explode ( ',', $gpmids );
			C::t ( 'common_member_grouppm' )->update_to_read_by_unread ( $_G ['uid'], $gpmidarr );
		}
		if ($uid) {
			$res = table_home_notification::isread ( $type, $uid );
			$data = array (
					'newprompt' => 0
			);
			DB::update('common_member', $data, 'uid = ' .intval($uid));
		}
		$data['rs'] = 1;
		return $data;
		}

}

?>