<?php
define('IN_MOBCENT',1);
require_once './abstarctMessageHeart.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../source/function/function_core.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once '../../uc_client/client.php';
require_once '../../uc_client/model/pm.php';


class messageHeartImpl_x25 extends abstarctMessageHeart {
	public function getMessageHeartObj() {
		
		$mod = new pmmodel();
		$info = new mobcentGetInfo();
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
		if(empty($accessSecret) && empty($accessToken))
		{
			$data_post['rs'] = 1;
			$data_post['list'] = array();
			return $data_post;exit();
		}else{
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
		$type = $_GET['type'];
		$data_pm["list"] = $list;
		$filter = in_array($_GET['filter'], array('newpm', 'privatepm', 'announcepm')) ? $_GET['filter'] : 'privatepm';
		switch($type)
		{
			case 0:
				$grouppms = $gpmids = $gpmstatus = array();
				$newpm = $newpmcount = 0;
				if($filter == 'privatepm' || $filter == 'newpm') {
					$result = uc_pm_list($_G['uid'], $page, $perpage, 'inbox', $filter, 200);
					$newpmcount = $result['count'];
					$list = $result['data'];
				}
				$newpmarr = uc_pm_checknew($_G['uid'], 1);
				$newpm = $newpmarr['newpm'];
				if($newpm > 0)
				{
					$data_pm['rs'] = 1;
					foreach($list as $key =>$row)
					{
						if($row['isnew'] ==1)
						{
							$data["nickName"]= $row['lastauthor'];
							$data["userId"]= (int)$row['lastauthorid'];
						}
						$data_pm['userList'][]= $data;
					}
					
				}
				else
				{
					$data_pm['rs'] = 1;
					$data_pm['userList'] = array();
				}
				return $data_pm;
				break;
			case 1:
				function json_to_array($web){
					$arr=array();
					foreach($web as $k=>$w){
						if(is_object($w)) $arr[$k]=json_to_array($w);  
						else $arr[$k]=$w;
					}
					return $arr;
				}
			
				$time = echo_urldecode($_GET['time']);
				$str = json_decode($time);
				$arr = json_to_array($str);
				$perpage = mob_perpage($perpage);
				if($page<1) $page = 1;
				$grouppms = $gpmids = $gpmstatus = array();
				$newpm = $newpmcount = 0;
				if($filter == 'privatepm' || $filter == 'newpm') {
					$result = DB::fetch_all('SELECT plid FROM '.UC_DBTABLEPRE.pm_members .' WHERE uid ='.$uid.' AND isnew =1');
					foreach ($result as $key =>$pid)
					{
						
						foreach($arr as $key =>$val)
						{
							$val['time'] =empty($val['time']) ?0: substr($val['time'],0,-3);
							$row = DB::fetch_all('SELECT * FROM '.UC_DBTABLEPRE.$mod->getposttablename($pid['plid']).' WHERE plid ='.$pid['plid'].' AND dateline > '.$val['time'].' AND authorid ='.$val['userId'].' order by dateline desc');
							$list [] =$row;
						}
						
					}
				
				}
				
				$totle=0;
				foreach($list as $key=>$rows)
				{
					foreach ($rows as $key => $value) {
						$user = getuserbyuid($value['authorid']);
						$plidarr[] = (int)$value['pmid'];
						$data["msg_id"]= (int)$value['plid'];
						$data['icon']='uc_server/avatar.php?uid='.$value['authorid'].'&size=small';
						$data["create_date"]= $value['dateline'].'000';
						$data["from_user_nickname"]= $user['username'];
						$data["content"]= preg_replace ( '/\[url\](\d+)\[\/url\]/is', '', $value['message']);;
						$data["from_user_id"]= (int)$value['authorid'];
						$data_pm["list"][]=$data;
						unset($data);
							
						$totle++;
					}
				}
				$start = 0;
				
				$data_pm["rs"]	= 1;
				$count = (int)$count;
				$data_pm["reply_notice_num"] = $totle;
				$data_pm["msg_total_num"] = $totle;
				$data_pm["hb_time"] = 15;
				$data_pm["icon_url"] = $Config['icon_url'];
				return $data_pm;
				break;
		}
		
			}
		}

?>