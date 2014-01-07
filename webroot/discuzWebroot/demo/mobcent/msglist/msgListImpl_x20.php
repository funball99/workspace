<?php
require_once './abstractMsgList.php';
require_once '../../source/class/class_core.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../../uc_client/client.php';
require_once '../model/table/x20/table_common_member.php';

require_once '../model/table/x20/mobcentDatabase.php';
class msgListImpl_x20 extends abstractMsgList {
	function getMsgListObj() { 

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
		$_GET ['do'] = 'pm';
		$isnew = intval ( $_GET ['isnew'] ) ? intval ( $_GET ['isnew'] ) : '';
		$list = array ();
		$plid = empty ( $_GET ['plid'] ) ? 0 : intval ( $_GET ['plid'] );
		$daterange = empty ( $_GET ['daterange'] ) ? 0 : intval ( $_GET ['daterange'] );
		$touid = empty ( $_GET ['touid'] ) ? 0 : intval ( $_GET ['touid'] );
		$page = empty ( $_GET ['page'] ) ? 0 : intval ( $_GET ['page'] );
		$perpage = empty ( $_GET ['pageSize'] ) ? 15 : intval ( $_GET ['pageSize'] );
		$opactives ['pm'] = 'class="a"';
		$filter = in_array ( $_GET ['filter'], array (
				'newpm',
				'privatepm',
				'announcepm'
		) ) ? $_GET ['filter'] : 'privatepm';
		
		$perpage = mob_perpage ( $perpage );
		if ($page < 1)
			$page = 1;
		$grouppms = $gpmids = $gpmstatus = array ();
		$newpm = $newpmcount = 0;
		if ($filter == 'privatepm' || $filter == 'newpm') {
			$result = uc_pm_list ( $_G ['uid'], $page, $perpage, 'inbox', $filter, 200 );
			$count = $result ['count'];
			$list = $result ['data'];
		}
		
		if (! empty ( $list )) {
			$today = $_G ['timestamp'] - ($_G ['timestamp'] + $_G ['setting'] ['timeoffset'] * 3600) % 86400;
			foreach ( $list as $key => $value ) {
				$value ['lastsummary'] = str_replace ( '&amp;', '&', $value ['lastsummary'] );
				$value ['lastsummary'] = preg_replace ( "/&[a-z]+\;/i", '', $value ['lastsummary'] );
				$value ['daterange'] = 5;
				if ($value ['lastdateline'] >= $today) {
					$value ['daterange'] = 1;
				} elseif ($value ['lastdateline'] >= $today - 86400) {
					$value ['daterange'] = 2;
				} elseif ($value ['lastdateline'] >= $today - 172800) {
					$value ['daterange'] = 3;
				} elseif ($value ['lastdateline'] >= $today - 604800) {
					$value ['daterange'] = 4;
				}
				$list [$key] = $value;
			}
		}
		$isblack = 0;
		if ($_GET ['subop'] == 'view') {
			foreach ( $list as $key => $value ) {
				$data_xx ['msg_relation_id'] = ( int ) $value ['pmid'];
				$data_xx ['created_date'] = $value ['dateline'] . '000';
				$data_xx ['user_id'] = ( int ) $value ['lastauthorid'];
				$data_xx ['to_user_id'] = ( int ) $value ['lastauthorid'] == $value ['touid'] ? $uid : $value ['touid'];
				$data_xx ['content'] = $value ['message'];
				$data_xx ['nick_name'] = $value ['lastauthor'];
				$data_xx ['to_nick_name'] = $value ['tousername'] == $value ['lastauthor'] ? '' : $value ['tousername'];
				$data_pm ['list'] [] = empty($data_xx)?array():$data_xx;
			}
		} else {
			foreach ( $list as $key => $value ) {
				$query = DB::query( 'SELECT count(*) as num FROM %t WHERE buid=%d and uid =%d', array (
						'home_blacklist',
						$uid,
						$value ['msgtoid']
							
				) );
				while($val = DB::fetch($query)) {
					if($val['num'])
					{
						$isblack = 1;
					}
					$data_xx ['msg_relation_id'] = ( int ) $value ['pmid'];
					$data_xx ['created_date'] = $value ['dateline'] . '000';
					$data_xx ['user_id'] = ( int ) $uid;
					$data_xx ['to_user_id'] = ( int ) $value ['lastauthorid'] == $value ['touid'] ? $uid : $value ['touid'];
					$data_xx ['content'] = $value ['message'];
					$data_xx ['nick_name'] = $value ['lastauthor'];
					$data_xx ['to_nick_name'] = $value ['tousername'] == $value ['lastauthor'] ? '' : $value ['tousername'];
					$data_xx ['uid'] = ( int ) $value ['msgtoid'];
					$data_xx ['name'] = $value ['tousername'];
					$data_xx ['icon'] = userIconImg ( $value ['msgtoid'] );
					$data_xx ['is_black'] = $isblack;
					$data_pm ['list'] [] = empty($data_xx)?array():$data_xx;
					$isblack = 0;
				}
			}
		}
		
		$N = ceil ( $count / $perpage );
		$page = empty ( $_GET ['page'] ) ? 0 : intval ( $_GET ['page'] );
		$data_pm ['icon_url'] = DISCUZSERVERURL;
		$data_pm ['rs'] = 1;
		$data_pm ['page'] = (Int)$page;
		$data_pm ['has_next'] = ($page >= $N || $N == 1) ? 0 : 1;
		return $data_pm;
		
		
	}

}

?>