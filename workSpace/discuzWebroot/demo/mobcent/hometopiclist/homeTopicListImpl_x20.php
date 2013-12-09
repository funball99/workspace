<?php
define ( 'IN_MOBCENT', 1 );
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../public/yz.php';
require_once '../tool/Thumbnail.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once libfile ( 'function/forumlist' );
require_once '../tool/constants.php';
require_once '../Config/public.php';
require_once '../model/table/x20/mobcentDatabase.php';
require_once ('./abstractHomeTopicList.php');
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/table_common_member.php';
class homeTopicListImpl_x20 extends abstractHomeTopicList {
	function getHomeTopicList() {
		$info = new mobcentGetInfo ();
		$topicInstance = new topic();
		$borderId = intval ( $_GET ['boardId'] );
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
			$uid = $arrAccess['user_id'];
			$space = $info->getUserInfo ( intval ($uid) );
			$forum = $info->getForumSub ( $borderId);
			$_G ['forum'] = array_merge ( $_G ['forum'], $forum );
			$checkObj = new check ();
			$resulst = $checkObj->viewperm ();
			if ($resulst ['error']) {
				echo $resulst ['message'];
				exit ();
			}
		
		
		$fid = $_GET ['boardId'];
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20;  
		if($page ==1)
		{
			$start_limit = 0;
		}
		else
		{
			$start_limit = ($page - 1) * $limit - 1;
		}
		$order = ($_GET ['sortby'] == 'publish' ? 'tid' : 'displayorder,lastpost');  
		$digest = $_GET ['digest'] ? array ( 3, 2, 1 ) : array ( 3, 2, 1, 0 );  
		$displayorder = $_GET ['displayorder'] ? array ( 3, 2, 1 ) : array ( 3, 2, 1, 0 ); 
		$keywords = $_GET ['keyword'];
		switch ($_GET ['sortby']) {
			case 'essence' :
				$digest = array (3,2,1);
				$jinghua = 1;
				break;
			case 'hot' :
				$stamp = 1;
				break;
		}
		
		$sort = $_GET ['sort'];
		switch ($sort) {
			case 1 :
				$sort = 'asc';
				break;
			case 0 :
			case '' :
				$sort = 'desc';
				break;
		}
		$forceindex = '';
		
		$filterarr1 = array ();
		$filterarr1 ['inforum'] = $fid; 
		$filterarr1 ['sticky'] = 4; 
		$filterarr1 ['digest'] = $digest;
		$filterarr1 ['displayorder'] = $displayorder;
		$filterarr1 ['keywords'] = $keywords;
		$tids =$info->forum_check_content($uid,$topicInstance);
 
		if ($stamp && empty ( $fid )) {
			$threadlist = C::t('forum_thread') ->fetch_all_hot($stamp,$start_limit,$limit,$fid ='',$tids);
		} elseif ($stamp && ! empty ( $fid )) {
			$parameter = array (
					'forum_thread',
					$stamp,
					$fid
			);
			$threadlist = C::t('forum_thread') ->fetch_all_hot($stamp,$start_limit,$limit,$fid );
		}elseif ($jinghua && empty ( $fid )) {  
			$threadlist_query = DB::query("SELECT * FROM ".DB::table('forum_thread')." WHERE (stamp=0 or icon=9 or digest in(1,2,3)) ORDER BY lastpost desc limit $start_limit,$limit");
			while ($threadlist_list = DB::fetch($threadlist_query)) {
				$threadlist[] = $threadlist_list;
			}
			$threadlist_num = DB::fetch(DB::query("SELECT count(*) as num FROM ".DB::table('forum_thread')." WHERE (stamp=0 or icon=9 or digest in(1,2,3))"));
		} else {
			$digest = implode(',', $digest);
			$displayorder = implode(',', $displayorder);
			$thread_query = DB::query ( "SELECT * FROM ".DB::table('forum_thread')." t WHERE  t.fid in(".$tids.") AND t.displayorder >'-1'  and t.displayorder in(".$displayorder.") and t.digest in(".$digest.") ORDER BY t.lastpost DESC  limit " . $start_limit. ','. $limit );
			while ($arr = DB::fetch($thread_query)) {
				$threadlist[$arr['tid']] = $arr;
					
			}
			$query = DB::query ( "SELECT count(*) as num FROM ".DB::table('forum_thread')." t WHERE t.fid in(".$tids.") AND t.displayorder >'-1'  and t.displayorder in(".$displayorder.") and t.digest in(".$digest.") ORDER BY t.lastpost DESC  ");
			$threadlist_num = DB::fetch($query);
		}
		require_once libfile ( 'function/attachment' );
		$smile_query = DB::query("SELECT code FROM ".DB::table('common_smiley')." WHERE type = 'smiley'");
		while ($smile_list = DB::fetch($smile_query)) {
			$smile_arr[] = $smile_list;
		}
		foreach($smile_arr as $sr){
			$smiles[]=$sr[code];
		}
		foreach($threadlist as $key=>$thread){
			$message_query=DB::fetch(DB::query("SELECT message FROM ".DB::table('forum_post')." WHERE first=1 AND tid=".(Int)$thread['tid']));
			preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i",  $message_query['message'] ,$matches);
			$patten  = array("\r\n", "\n", "\r");
			$data_subject = str_replace($matches[1], '', $message_query ['message']);
			$data_subject =str_replace($patten, '', $data_subject);
			$data_subject = preg_replace("#(\w*)\[.*?\](\w*)#","$1$2",$data_subject);
			foreach($smiles as $si){
				$data_subject =str_replace($si, '', $data_subject);
			}
			$data_subject =trim($data_subject);
			$data_subject = sub_str($data_subject, 0,40);
			$threadlist[$key][message]=$data_subject;
		}
		foreach ( $threadlist as $k => $group ) {
			$query = DB::query ( "SELECT status from ".DB::table('forum_forum')." where fid=".$group ['fid']);
			$status = DB::fetch($query);
			
			if($status['status'] == 0)
			{
				continue;
			}
			if ($group ["attachment"] == 2) {
				$pic = C::t('forum_thread') ->fetch_all_threadimage($group);
				$pic_path = $topicInstance->parseTargeImage($pic);
			}
			if($group ["special"] ==2)
			{
				$query = DB::query("SELECT * FROM ".DB::table('forum_trade')." WHERE tid='".$group['tid']."'  ORDER BY displayorder");
				while($trade = DB::fetch($query)) {
					$tradesaids[] = $trade['aid'];
					$tradespids[] = $trade['pid'];
				}
					
				$specialadd2 = 1;
				$tradespids = dimplode($tradespids);
				if($tradespids) {
					$query = DB::query("SELECT * FROM ".DB::table(getattachtablebytid($group['tid']))." WHERE pid IN ($tradespids)");
					while($attach = DB::fetch($query)) {
						if($attach['isimage'] && is_array($tradesaids) && in_array($attach['aid'], $tradesaids)) {
							$trades[$attach['pid']]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
							$trades['thumb'] = $attach['thumb'] ? getimgthumbname($trades[$attach['pid']]['attachurl']) : $trades[$attach['pid']]['attachurl'];
							$trades[$attach['pid']]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
						}
					}
				}
				
				if(!empty($trades))
				{
					$trades['thumb'] = str_replace('forum/', '', $trades['thumb']);
					$pic_path = $topicInstance ->parseTradeTopicImg($trades);
				}
			}
			$data ['board_id'] = ( int ) $group ['fid']; 
			$data ['topic_id'] = ( int ) $group ['tid'];
      		$title = sub_str($group ['subject'], 0, 15);
			$data ['title'] = $title; 
			$data ['subject'] = $group ['message'];
			$data ['user_id'] = ( int ) $group ['authorid']; 
			$data ['last_reply_date'] = ($group ['lastpost']) . "000";
			$data ['user_nick_name'] = $group ['author'];
			$data ['hits'] = ( int ) $group ['views']; 
			if($group ["special"]==1){
				$data ['vote'] = (int)1;
			}else{
				$data ['vote'] = (int)0;
			}
			$data ['replies'] = ( int ) $group ['replies'];
			$data ['top'] = ( int ) $group ['displayorder']>0 || ( int ) $group ['icon'] ==13 || (int ) $group ['stamp'] ==4? 1 : 0; 
			$data ['status'] = ( int ) $group ['status'];
			$data ['essence'] = ( int ) $group ['digest'] >0|| ( int ) $group ['icon'] ==9 || (int ) $group ['stamp'] ==0? 1 : 0; 
			$data ['hot'] = ( int ) $group ['stamp'] ==1|| ( int ) $group ['icon'] ==10? 1 : 0;
			if($pic_path)
			{
				$data ['pic_path'] = $pic_path;
			}
			else
			{
				$data ['pic_path'] = '';
			}
			unset ( $pic_path );
			$data_thread [] = $data;
		}
		
		$announcement = C::t ( 'forum_announcement' )->fetch_all_by_displayorder ();
		foreach ( $announcement as $k => $announ ) {
			$data_announ [] = array (
					"announce_id" => $announ ["id"],
					"author" => $announ ["author"],
					"board_id" => '',
					"forum_id" => '',
					"start_date" => $announ ["starttime"] . '000',
					"title" => $announ ["subject"]
			);
		}
		
		if ($stamp) {
			$count = C::t('forum_thread') ->count_hot_search($stamp = "stamp='".$stamp."'",$displayorder,$fid,$tids);
		} else {
			$count = $threadlist_num['num'];
		}
		
		$thread_info = array (
				"img_url" => DISCUZSERVERURL,
				"total_num" => (Int)$count,
				"page" => (Int)$page,
				/*"anno_list" => empty($data_announ)?array():$data_announ,*/
				'list' => empty($data_thread)?array():$data_thread,
				'rs' => (Int)1
		);
		if ($fid == '') {
			unset ( $thread_info ['anno_list'] );
		}
		return $thread_info;
		}

}

?>