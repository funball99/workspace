<?php
define('IN_MOBCENT',1);
require_once './abstarctMessageHeart.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once '../../uc_client/client.php';


class messageHeartImpl_x25 extends abstarctMessageHeart {
	public function getMessageHeartObj() {
		$info = new mobcentGetInfo();
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $_G ['uid'] = $arrAccess['user_id'];
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		$_GET['do'] = 'pm';
		$_GET['subop'] = $_GET['subop'] ;
		$isnew = 1;
		$list = array();
		$plid = empty($_GET['plid'])?0:intval($_GET['plid']);
		$daterange = empty($_GET['daterange'])?0:intval($_GET['daterange']);
		$touid = empty($_GET['touid'])?0:intval($_GET['touid']);
		$page = empty($_GET['page']) ? 0 : intval($_GET['page']);
		$perpage = empty($_GET['pageSize']) ? 15 : intval($_GET['pageSize']);
		$data_pm["list"] = $list;
		
		$filter = in_array($_GET['filter'], array('newpm', 'privatepm', 'announcepm')) ? $_GET['filter'] : 'privatepm';
		
		$perpage = mob_perpage($perpage);
		if($page<1) $page = 1;
		$grouppms = $gpmids = $gpmstatus = array();
		$newpm = $newpmcount = 0;
		if($filter == 'privatepm' || $filter == 'newpm') {
			$result = uc_pm_list($_G['uid'], $page, $perpage, 'inbox', $filter, 200);
			$newpmcount = $result['count'];
			$list = $result['data'];
		}
		
		$totle=0;
		foreach ($list as $key => $value) {
			if($value['isnew'] != $isnew && $isnew!=''){
				break;
			}
			$plidarr[] = (int)$value['pmid'];
			$data["msg_id"]= (int)$value['pmid'];
			$data['icon']='uc_server/avatar.php?uid='.$uid.'&size=small';
			$data["create_date"]= $value['dateline'].'000';
			$data["from_user_nickname"]= $value['lastauthor'];
			$data["content"]= $value['message'];
			$data["from_user_id"]= (int)$value['lastauthorid'];
			$data_pm["list"][]=$data;
			unset($data);
		
			$totle++;
		}
		$start = 0;
		
		$data_pm["rs"]	= 1;
		$count = (int)$count;
		$data_pm["reply_notice_num"] = $totle;
		$data_pm["msg_total_num"] = $totle;
		$data_pm["hb_time"] = 15;
		$data_pm["icon_url"] = $Config['icon_url'];
		return $data_pm;
			}
		}

?>