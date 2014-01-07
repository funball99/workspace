<?php
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../source/class/table/table_forum_forumfield.php';
require_once '../../config/config_ucenter.php';
require_once libfile('function/forumlist');
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/img_do.php';
require_once '../public/yz.php';
require_once '../Config/public.php';
require_once '../tool/Thumbnail.php';
require_once '../tool/constants.php';
require_once ('./abstractTopicList.php');
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_common_member.php';
require_once '../model/table/x25/table_forum_typeoptionvar.php';
require_once '../model/table/x25/table_forum_threadtype.php';
require_once '../model/table/x25/table_forum_threadclass.php';
require_once '../../source/class/table/table_forum_forum.php';
define('ALLOWGUEST', 1);
C::app ()->init();
require_once '../public/mobcentDatabase.php';

class topicListImpl_x25 extends abstractTopicList {
	function getSubBoardList() {
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
		$gid = intval(getgpc('boardId'));
		$catlist = $forumlist = $sublist = $forumname = $collapse = $favforumlist = array();
		$gquery = C::t('forum_forum')->fetch_all_info_by_fids($gid);
		$query = C::t('forum_forum')->fetch_all_info_by_fids(0, 'available', 0, $gid, 1, 0, 0, 'sub');
		$query = array_merge($gquery, $query);
		$fids = array();
		foreach($query as $forum) {
		$forum['extra'] = dunserialize($forum['extra']);
		if(!is_array($forum['extra'])) {
		$forum['extra'] = array();
		}
		if($forum['type'] != 'group') {
			$threads += $forum['threads'];
			$posts += $forum['posts'];
			$todayposts += $forum['todayposts'];
			if(forum_2($forum)) {
			$forum['orderid'] = $catlist[$forum['fup']]['forumscount'] ++;
			$forum['subforums'] = '';
			$forumlist[$forum['fid']] = $forum;
			$catlist[$forum['fup']]['forums'][] = $forum['fid'];
			$fids[] = $forum['fid'];
			}
			} else {
			$forum['collapseimg'] = 'collapsed_no.gif';
				$collapse['category_'.$forum['fid']] = '';
		
				if($forum['moderators']) {
				$forum['moderators'] = moddisplay($forum['moderators'], 'flat');
			}
					$catlist[$forum['fid']] = $forum;
		
							$navigation = '<em>&rsaquo;</em> '.$forum['name'];
							$navtitle_g = strip_tags($forum['name']);
			}
			}
		$query = C::t('forum_forum')->fetch_all_subforum_by_fup($fids);
		foreach($query as $forum) {
		if($_G['setting']['subforumsindex'] && $forumlist[$forum['fup']]['permission'] == 2) {
		$forumurl = !empty($forum['domain']) && !empty($_G['setting']['domain']['root']['forum']) ? 'http://'.$forum['domain'].'.'.$_G['setting']['domain']['root']['forum'] : 'forum.php?mod=forumdisplay&fid='.$forum['fid'];
		$forumlist[$forum['fup']]['subforums'] .= '<a href="'.$forumurl.'"><u>'.$forum['name'].'</u></a>&nbsp;&nbsp;';
		}
		$forumlist[$forum['fup']]['threads'] 	+= $forum['threads'];
		$forumlist[$forum['fup']]['posts'] 	+= $forum['posts'];
		$forumlist[$forum['fup']]['todayposts'] += $forum['todayposts'];
		}
		unset($forum_access, $forum_fields);
		if(is_array($gquery)){
			$topicInstance = new topic();
			/*fenlei*/
			$forumfield= C::t ( 'forum_forumfield' )->fetch_all_by_fid ($_GET['boardId']);
			$fenlei=unserialize($forumfield[$_GET['boardId']][threadsorts]);
			foreach($fenlei['types'] as $fl_key=>$fl_val){
				$key_arr[]=$fl_key;
				$val_arr[]=$fl_val;
			}
			for($i=0;$i<count($key_arr);$i++){
				$fenlei_arr[$i]['classificationTop_id']=$key_arr[$i];
				$fenlei_arr[$i]['classificationTop_name']=$topicInstance->replaceHtmlAndJs($val_arr[$i]);
			}
			/* end fenlei */

			/*lei bie*/
			$forum = C::t('forum_forum')->fetch_info_by_fid($_GET['boardId']);
			$leibie_query=unserialize($forum[threadtypes]);
			foreach($leibie_query[types] as $leibiekey=>$leibieval){
				$leibie_list[classificationType_id]=$leibiekey;
				$leibie_list[classificationType_name]=$topicInstance->replaceHtmlAndJs($leibieval);
				$leibie_arr[]=$leibie_list;
			}
			/* end lei bie */
			 //print_r($query);exit;
		if(is_array($query)){
			foreach ( $query as $k => $forum ) {
				$data_forum [] = array (
				"board_id"			=>(int)$forum["fid"], 		 
				"board_name"		=>$forum["name"],		 
				"td_posts_num"		=>(int)$forum["todayposts"],	 
				"topic_total_num"	=>(int)$forum["threads"],	 
				"posts_total_num"	=>(int)$forum["posts"],	 
				"last_posts_date"	=>$forumlist[$forum["fid"]]["lastpost"]['dateline'].'000',	 
				);
			}
		}else{
			$data_forum[]= array();
		}
	    foreach ( $gquery as $k => $cat ) {
			$data_cat  = array (
							"board_category_id"		=>(int)$cat["fid"],  
							"board_category_name"	=>$cat["name"], 
							"board_category_type"   =>2,	 
							"board_list"			=>empty($data_forum)?array():$data_forum,	 
							"classificationTop_list"=>empty($fenlei_arr)?array():$fenlei_arr,
							"classificationType_list"=>empty($leibie_arr)?array():$leibie_arr,
							);
			}
		}else{
			$data_cat ['cat'] []= array();
		}
		return $data_cat;
	}

	function getTopicList() {
		require_once libfile ( 'function/forumlist' );
		
		require_once '../public/mobcentDatabase.php';
		require_once '../model/table/x25/table_forum_announcement.php';
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
		global $_G;
		$_G['groupid'] =$group['groupid'];
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$userId = $arrAccess['user_id'];
		$space = $info->getUserInfo ( intval ( $userId ) );
		$_G ['fid'] = intval ( $_GET ['boardId'] );
		if ($_G ['fid']) {
			$_G = array_merge ( $_G, $space );
			$forum = $info->getForumSub ( $_G ['fid'] );
			$_G ['forum'] = array_merge ( $_G ['forum'], $forum );
			$checkObj = new check ();
			$resulst = $checkObj->viewperm ();
			if ($resulst ['error']) {
				echo $resulst ['message'];
				exit ();
			}
		}
		$fid = $_GET ['boardId'];
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20;
		$start_limit = ($page - 1) * $limit; 
		$order = ($_GET ['sortby'] == 'publish' ? 'tid' : 'displayorder DESC,lastpost'); 
		$digest = $_GET ['digest'] ? array (
				3,
				2,
				1
		) : array (
				3,
				2,
				1,
				0
		);
		$displayorder = $_GET ['displayorder'] ? array (
				3,
				2,
				1
		) : array (
				3,
				2,
				1,
				0
		);
		$displayorder = $_GET ['displayorder'] ? array (
				4,
				3,
				2,
				1
		) : array (
				0,
				1,
				2,
				3,
				4
		);
		$keywords = $_GET ['keyword'];
		switch ($_GET ['sortby']) {
			case 'essence' :
				$digest = array (
				3,
				2,
				1
				);
				$jinghua = 1;
				break;
			case 'hot' :
				$stamp = 1;
				break;
			case 'top' :
				$zhiding = 1;
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
		$filterarr1 ['icon'] =13;
		$table = array('forum_thread');
		$threadlist_top =  DB::fetch_all ( "SELECT * FROM %t WHERE displayorder=3 and fid != ".$fid, $table);
		if(empty($_GET ['digest'])&& empty($_GET ['displayorder']) && empty($_GET ['sortby']) && $page ==1)
		{
			$announcement = C::t ( 'forum_announcement' )->fetch_all_by_displayorder ();
			foreach ( $announcement as $k => $announ ) {
				$data_announ [] = array (
						"announce_id" => $announ ["id"],
						"author" => $announ ["author"],
						"board_id" => '',
						"forum_id" => '',
						"start_date" => $announ ["starttime"] . '000',
						"title" =>preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$announ ["subject"])
				);
			}
		}
		$topicInstance = new topic();
		
		
		
		if ($stamp && empty ( $fid )) {  
			$digest =" AND (t.icon = 10 or t.stamp = 1)";
			$parameter = array (
					'forum_thread',
					$stamp ='stamp ='.$stamp.$digest
			);
			
			$threadlist = DB::fetch_all ( "SELECT * FROM %t WHERE ".$stamp . DB::limit ( $start_limit, $limit ), $parameter, 'tid' );
		} elseif ($stamp && ! empty ( $fid )) {  
			$parameter = array (
					'forum_thread',
			);
			$tids =$info ->forum_display($fid,$topicInstance);
			$threadlist = DB::fetch_all ( "SELECT * FROM %t t WHERE  t.displayorder >'-1'  AND (t.icon =10 or stamp =1) AND t.fid in(".$tids.") AND t.fid = ".$fid." ORDER BY t.tid desc" . DB::limit ( $start_limit, $limit ), $parameter, 'tid' );
		
		}elseif ($zhiding && ! empty ( $fid )) {
			$thisfup = DB::fetch(DB::query("SELECT fup FROM ".DB::table('forum_forum')." WHERE fid=".$fid));
			$thisfids_query = DB::query("SELECT fid FROM ".DB::table('forum_forum')." WHERE fup=".$thisfup[fup]);
			while ($thisfids_list = DB::fetch($thisfids_query)) {
				$thisfids[] = $thisfids_list;
			}
			foreach($thisfids as $tfs){
				$myfids_arr[]=$tfs[fid];
			}
			$myfids=implode(',', $myfids_arr);
			
			$zd3_query = DB::query("SELECT * FROM ".DB::table('forum_thread')." WHERE displayorder=3 ORDER BY tid desc limit $start_limit,$limit");
			while ($zd3_list = DB::fetch($zd3_query)) {
				$zd3[] = $zd3_list;
			}	
			$zd2_query = DB::query("SELECT * FROM ".DB::table('forum_thread')." WHERE displayorder=2 AND fid IN(".$myfids.") ORDER BY tid desc limit $start_limit,$limit");
			while ($zd2_list = DB::fetch($zd2_query)) {
				$zd2[] = $zd2_list;
			}
			$zd1_query = DB::query("SELECT * FROM ".DB::table('forum_thread')." WHERE displayorder=1 AND fid=".$fid." ORDER BY tid desc limit $start_limit,$limit");
			while ($zd1_list = DB::fetch($zd1_query)) {
				$zd1[] = $zd1_list;
			}
			$z3=empty($zd3)?array():$zd3;
			$z2=empty($zd2)?array():$zd2;
			$z1=empty($zd1)?array():$zd1;
			$threadlist=array_merge($z3,$z2,$z1);
			$zd_count=(Int)count($threadlist);
		}elseif ($jinghua && ! empty ( $fid )) { 
			$threadlist_query = DB::query("SELECT * FROM ".DB::table('forum_thread')." WHERE (stamp=0 or icon=9 or digest in(1,2,3)) AND displayorder >'-1' AND fid=".$fid." ORDER BY lastpost desc limit $start_limit,$limit");
			while ($threadlist_list = DB::fetch($threadlist_query)) {
				$threadlist[] = $threadlist_list;
			}
		}else {  
			//$threadlist = C::t ( 'forum_thread' )->fetch_all_search ( $filterarr1, $tableid, $start_limit, $limit, $order, $sort, $forceindex = '' );
			$threadlist_query = DB::query("SELECT * FROM ".DB::table('forum_thread')." WHERE fid=".$fid." AND displayorder >'-1' ORDER BY lastpost desc limit $start_limit,$limit");
			while ($threadlist_list = DB::fetch($threadlist_query)) {
				$threadlist[] = $threadlist_list;
			}
		}
		
		/*if($page ==1 && empty($_GET ['sortby'])){
			$threadlist =array_merge($threadlist_top,$threadlist);
		}*/
		// print_r($threadlist);exit;
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
			$ForumImg = DB::fetch_first("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$group['tid']." AND p.first =1");
			if ($ForumImg ["attachment"] == 2) {
				$parameter = array (
						'forum_threadimage',
						( int ) $group ['tid'],
				);
				$pic = DB::fetch_first ( "SELECT * from %t where tid=%d", $parameter );
				
				if(!empty($pic)){ 
					$filename = $topicInstance->parseTargeImage($pic); 
				}
			}
			if($group ["special"] ==2)
			{
				$query = C::t('forum_trade')->fetch_all_thread_goods($group['tid']);
				foreach($query as $trade) {
					$tradesaids[] = $trade['aid'];
					$tradespids[] = $trade['pid'];
				}
				$specialadd2 = 1;
				if($tradespids) {
					foreach(C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$group['tid'], 'pid', $tradespids) as $attach) {
						if($attach['isimage'] && is_array($tradesaids) && in_array($attach['aid'], $tradesaids)) {
							$trades[$attach['pid']]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
							$trades['thumb'] = $attach['thumb'] ? getimgthumbname($trades[$attach['pid']]['attachurl']) : $trades[$attach['pid']]['attachurl'];
							$trades[$attach['pid']]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
						}
					}
				}
				if(!empty($trades))
				{
					$topicInstance = new topic();
					$trades['thumb'] = str_replace('forum/', '', $trades['thumb']);
					$filename = $topicInstance ->parseTargeImage($trades);
				}
			}
			$data ['board_id'] = ( int ) $group ['fid'];
			$boardName = DB::fetch(DB::query("SELECT name FROM ".DB::table('forum_forum')." WHERE fid=".$group ['fid']));
			$data ['board_name'] = $boardName[name];
			$data ['topic_id'] = ( int ) $group ['tid'];
			$data ['type_id'] = ( int ) $group ['typeid'];
			$data ['sort_id'] = ( int ) $group ['sortid'];
			if($group ["special"]==1){
				$data ['vote'] = (int)1;
			}else{
				$data ['vote'] = (int)0;
			}
	    	$data ['title'] = $group ['subject']; 
	    	$data ['subject'] = $group ['message'];
			$data ['user_id'] = ( int ) $group ['authorid'];
			$data ['last_reply_date'] = ($group ['lastpost']) . "000";
			if(empty($group ['author']))
			{
				$data ['user_nick_name'] =Common::get_unicode_charset('\u533f\u540d');
			}
			else
			{
				$data ['user_nick_name'] = $group ['author'];
			}
			$data ['hits'] = ( int ) $group ['views'];
			$data ['replies'] = ( int ) $group ['replies'];
			$data ['top'] = ( int ) $group ['displayorder'] > 0 || ( int ) $group ['icon'] ==13 || (int ) $group ['stamp'] ==4? 1 : 0;
			$data ['status'] = ( int ) $group ['status'];
			$data ['essence'] = ( int ) $group ['digest'] >0 || ( int ) $group ['icon'] ==9 || (int ) $group ['stamp'] ==0? 1 : 0;
			$data ['hot'] = ( int ) $group ['stamp'] ==1 || ( int ) $group ['icon'] ==10? 1 : 0;
			if ($filename) {
				$data ['pic_path'] = $filename;
			}else {
				$data ['pic_path'] = '';
			}
			unset ( $filename );
			$ret_pic_path = '';
			$data_thread [] = $data;
		}
		 
		/*[fen lei ming cheng]*/
		$forum = C::t('forum_forum')->fetch_info_by_fid($_GET['boardId']);
		$isfenleishow_arr=unserialize($forum[threadsorts]);
		$isleibieshow_arr=unserialize($forum[threadtypes]);
		$isfenleishow=$isfenleishow_arr[prefix];
		$isleibieshow=$isleibieshow_arr[prefix];
		
		if($isfenleishow==1){
			for($i=0;$i<count($data_thread);$i++){
				$fenlei_name= C::t ( 'forum_threadtype' )->fetch_name_by_typeid ($data_thread[$i][sort_id]);
				foreach($fenlei_name as $fl){
					$fenleimingcheng=$topicInstance->replaceHtmlAndJs($fl['name']);
					$data_thread[$i][title]="[".$fenleimingcheng."]".$topicInstance->replaceHtmlAndJs($data_thread[$i][title]);
				}
			}
		}
		if($isleibieshow==1 || $isleibieshow==2){
			for($i=0;$i<count($data_thread);$i++){
				$fenlei_type= C::t ( 'forum_threadclass' )->fetch_all_by_typeid ($data_thread[$i][type_id]);
				foreach($fenlei_type as $ftype){
					$fenleitypemingcheng=$topicInstance->replaceHtmlAndJs($ftype['name']);
					$data_thread[$i][title]="[".$fenleitypemingcheng."]".$data_thread[$i][title];
				}
			}
		}
		
		/*[end fen lei ming cheng]*/
		
		if ($stamp && empty($fid)) { 
			$parameter = array (
					'forum_thread',
			);
			$tids =$info ->forum_display($fid,$topicInstance);
			$num = DB::fetch_all ( "SELECT count(*) as num FROM %t t WHERE  t.displayorder >'-1'  AND (t.icon =10 or stamp =1) AND t.fid in(".$tids.") ORDER BY t.tid desc limit 1", $parameter, 'tid' );
			$count = $num ['num'];
			
		} else if($stamp && !empty($fid)){ 
			$parameter = array (
					'forum_thread',
			);
			$tids =$info ->forum_display($fid,$topicInstance);
			$num = DB::fetch_all ( "SELECT count(*) as num FROM %t t WHERE  t.displayorder >'-1'  AND (t.icon =10 or stamp =1) AND t.fid in(".$tids.") AND t.fid = ".$fid." ORDER BY t.tid desc limit 1", $parameter, 'tid' );
			$count = $num [0]['num'];
		}elseif ($zhiding && ! empty ( $fid )) {
			$count = $zd_count;
		}else{ 
			$count = C::t ( 'forum_thread' )->count_search ( $filterarr1, $tableid );
		} 
		 
		$thread_info = array (
				"img_url" => DISCUZSERVERURL,
				"total_num" => (Int)$count,
				"page" => (Int)$page,
				"anno_list" => empty($data_announ)?array():$data_announ,
				'list' => empty($data_thread)?array():$data_thread,
				'rs' => 1
		);

		if ($fid == '') {
			unset ( $thread_info ['anno_list'] );
		}
		return $thread_info;
		}

}

?>