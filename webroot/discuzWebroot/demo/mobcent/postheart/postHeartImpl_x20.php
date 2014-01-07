<?php
require_once './abstarctPostHeart.php';
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once libfile ( 'function/forumlist' );
require_once '../../uc_client/client.php';
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/mobcentDatabase.php';

class postHeartImpl_x20 extends abstarctPostHeart {
	public function getPostHeartObj() {
		$info = new mobcentGetInfo();
		$topicInstance = new topic();
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
		setglobal('groupid', $group['groupid']);
		global $_G;
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$_G['uid'] = $uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		$_GET ['do'] = 'pm';
		$_GET ['subop'] = $_GET ['subop'];
		$isnew = 1;
		$plid = empty ( $_GET ['plid'] ) ? 0 : intval ( $_GET ['plid'] );
		$daterange = empty ( $_GET ['daterange'] ) ? 0 : intval ( $_GET ['daterange'] );
		$touid = empty ( $_GET ['touid'] ) ? 0 : intval ( $_GET ['touid'] );
		$page = empty ( $_GET ['page'] ) ? 0 : intval ( $_GET ['page'] );
		$perpage = empty ( $_GET ['pageSize'] ) ? 15 : intval ( $_GET ['pageSize'] );
		
		try {
			$filter = in_array ( $_GET ['filter'], array (
					'newpm',
					'privatepm',
					'announcepm'
			) ) ? $_GET ['filter'] : 'privatepm';
		
			$perpage = mob_perpage ( $perpage );
			if ($page < 1)
				$page = 1;
			$start = $page == 1 ? 0 : ($page - 1) * $perpage - 1;  
			$grouppms = $gpmids = $gpmstatus = array ();
			$newpm = $newpmcount = 0;
		
			$totle = 0;
			$type = "type = 'post' or type = 'at'";
			$start = 0;
			$new = intval($isnew);
			$type = $type ? ' AND ('.$type.')': '';
			$new = ' AND new = '. $new;
			$tids =$info->forum_check_content($uid,$topicInstance);
			$reply_count = C::t('home_notification')->count_by_uid ( $uid, $isnew, "type = 'post'",$tids);
			$at_reply_count = C::t('home_notification')->count_by_uid ( $uid, $isnew, "type = 'at'",$tids);
			$data_pm ["rs"] = 1;
			$data_pm ['relational_notice_num'] = ( int ) $at_reply_count['num'];
			$data_pm ["reply_notice_num"] = ( int )$reply_count['num'];
			$data_pm ["hb_time"] = 60;
			$data_pm ["icon_url"] = '';
		
			if ($plidarr [0]) {
				$res = uc_pm_readstatus ( $uid, array (), $plidarr, 0 );
			}
		} catch ( Exception $e ) {
			$data_pm ["rs"] = 0;
			$data_pm ['errcode'] = ( int ) 99999999;
		}
		return $data_pm;
		exit ();
		}

}

?>