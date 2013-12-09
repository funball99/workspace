<?php
require_once './abstarctPostHeart.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../model/table_home_notification.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once '../model/table/x25/topic.php';
require_once libfile ( 'function/forumlist' );
class postHeartImpl_x25 extends abstarctPostHeart {
	public function getPostHeartObj() {
		$topicInstance = new topic();
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
		global $_G;
		$_G['groupid'] =$group['groupid'];
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $_G ['uid'] =$arrAccess['user_id'];
		if(empty($_G ['uid']))
		{
			return $info -> userAccessError();
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
			$start = 0;
			$tids =$info ->forum_display($fid,$topicInstance);
			
			$reply_count = table_home_notification::count_by_uid ( $uid, $isnew, "type = 'post'" ,$tids);
		  	$at_reply_count = table_home_notification::count_by_uid ( $uid, $isnew, "type = 'at'" ,$tids);
			$data_pm ["rs"] = 1;
			$data_pm ['relational_notice_num'] = ( int ) $at_reply_count;
			$data_pm ["reply_notice_num"] = ( int ) $reply_count;
			$data_pm ["hb_time"] = 180;
			$data_pm ["icon_url"] = '';
		
			if ($plidarr [0]) {
				$res = uc_pm_readstatus ( $uid, array (), $plidarr, 0 );
			}
		} catch ( Exception $e ) {
			$data_pm ["rs"] = 0;
			$data_pm ['errcode'] = ( int ) 99999999;
		}
		return $data_pm;
}
}
?>