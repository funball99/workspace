<?php
define ( 'IN_MOBCENT', 1 );
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../config/config_ucenter.php';
require_once '../../source/function/function_forumlist.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/img_do.php';
require_once '../tool/Thumbnail.php';
require_once '../../source/function/function_forum.php';
require_once '../public/yz.php';
require_once '../tool/constants.php';
require_once './abstractTopicList.php';
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/table_common_member.php';
require_once '../model/table/x20/table_forum_announcement.php';
require_once '../model/table/x20/mobcentDatabase.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();

class topicListImpl_x20 extends abstractTopicList {
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
		$sublist = array();
		$sql = !empty($_G['member']['accessmasks']) ? "SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.domain, ff.description, ff.moderators, ff.icon, ff.viewperm, ff.extra, ff.redirect, a.allowview FROM ".DB::table('forum_forum')." f
								LEFT JOIN ".DB::table('forum_forumfield')." ff ON ff.fid=f.fid
								LEFT JOIN ".DB::table('forum_access')." a ON a.uid='$_G[uid]' AND a.fid=f.fid
								WHERE fup='$gid' AND status>'0' AND type='sub' ORDER BY f.displayorder"
								: "SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.domain, ff.description, ff.moderators, ff.icon, ff.viewperm, ff.extra, ff.redirect FROM ".DB::table('forum_forum')." f
								LEFT JOIN ".DB::table('forum_forumfield')." ff USING(fid)
								WHERE f.fup='$gid' AND f.status>'0' AND f.type='sub' ORDER BY f.displayorder";
		$query = DB::query($sql);
		while($sub = DB::fetch($query)) {
			$sub['extra'] = unserialize($sub['extra']);
			if(!is_array($sub['extra'])) {
				$sub['extra'] = array();
			}
			if(forum_2($sub)) {
				$sub['orderid'] = count($sublist);
				$sublist[] = $sub;
			}
		}
		
		if(is_array($sublist)){
		$topicInstance = new topic();
		/*fenlei*/
		$query_field= DB::query("SELECT threadsorts FROM ".DB::table('forum_forumfield')." WHERE fid=".$_GET['boardId']);
		while($value_field = DB::fetch($query_field)) {
			$fenlei=unserialize($value_field[threadsorts]);
		}
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
		$forum=	DB::fetch_first("SELECT ff.*, f.* FROM ".DB::table('forum_forum')." f LEFT JOIN ".DB::table('forum_forumfield')." ff ON ff.fid=f.fid WHERE f.fid=".$_GET['boardId']);
		$leibie_query=unserialize($forum[threadtypes]);
		foreach($leibie_query[types] as $leibiekey=>$leibieval){
			$leibie_list['classificationType_id']=$leibiekey;
			$leibie_list['classificationType_name']=$topicInstance->replaceHtmlAndJs($leibieval);
			$leibie_arr[]=$leibie_list;
		}
		/* end lei bie */
			
		foreach ( $sublist as $k => $forum ) {
			$data_forum [] = array (
					"board_id"			=>(int)$forum["fid"],
					"board_name"		=>$forum["name"],
					"td_posts_num"		=>(int)$forum["todayposts"],
					"topic_total_num"	=>(int)$forum["threads"],
					"posts_total_num"	=>(int)$forum["posts"],		
					"last_posts_date"	=>$forum['lastpost']['dateline'].'000',	
			);
		}
		
	}else{
		$data_forum= array();
		
	}
	$data =C::t('forum_thread') -> fetch_name_by_fid($gid);
		$data_cat  = array (
				"board_category_id"		=>$gid, 
				"board_category_name"	=>$data['name'],					
				"board_category_type"   =>2,
				"board_list"			=>empty($data_forum)?array():$data_forum,	
				"classificationTop_list"=>empty($fenlei_arr)?array():$fenlei_arr,
				"classificationType_list"=>empty($leibie_arr)?array():$leibie_arr,
		);
		return $data_cat;
	}

function getTopicList() {
require_once '../model/table/x20/mobcentDatabase.php';
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
		setglobal('groupid', $group['groupid']);
		global $_G;
	$_G ['fid'] = intval ( $_GET ['boardId'] );
	$checkObj = new check ();
	if ($resulst ['error']) {
		echo $resulst ['message'];
		exit ();
	}

$fid = $_GET ['boardId'];
$page = $_GET ['page'] ? $_GET ['page'] : 1;
$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20; 
$isPublish = ($_GET['sortby'] == 'publish' ? "dateline" : "lastpost"); 

if($page ==1)
{
	$start_limit = 0;
}
else
{
	$start_limit = ($page - 1) * $limit; 
}
$order = ($_GET ['sortby'] == 'publish' ? 'tid' : 'displayorder DESC,lastpost'); 
                                                                           
$fidsql = '';
if($_G['forum']['relatedgroup']) {
	$relatedgroup = explode(',', $_G['forum']['relatedgroup']);
	$relatedgroup[] = $_G['fid'];
	$fidsql = " t.fid IN(".dimplode($relatedgroup).")";
} else {
	$fidsql = " t.fid='{$fid}'";
}                                                                           
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
$picNew = new topic();
$filterarr1 = array ();
$filterarr1 ['inforum'] = $fid; 
$filterarr1 ['digest'] = $digest;
$filterarr1 ['displayorder'] = $displayorder;
$filterarr1 ['keywords'] = $keywords;
$tids =$info->forum_check_content($uid,$picNew);
$threadtableids = !empty($_G['cache']['threadtableids']) ? $_G['cache']['threadtableids'] : array();
$threadtable = $_G['gp_archiveid'] && in_array($_G['gp_archiveid'], $threadtableids) ? "forum_thread_{$_G['gp_archiveid']}" : 'forum_thread';
$threadlist_top =array();
$query =  DB::query ( "SELECT * FROM ".DB::table('forum_thread')." WHERE displayorder=3 and fid != ".$fid);
while($arr = DB::fetch($query)){
	$threadlist_top[] =$arr;
}
if ($stamp && empty ( $fid )) {
	$digest =" AND (t.icon = 10 or t.stamp = 1)";
	$threadlist = C::t('forum_thread') ->fetch_all_hot("t.stamp='".$stamp."'".$digest,$start_limit,$limit,$fid ='');
} elseif ($stamp && ! empty ( $fid )) {
	$digest =" AND (t.icon = 10 or t.stamp = 1)";
	$threadlist = C::t('forum_thread') ->fetch_all_hot("t.stamp='".$stamp."'".$digest,$start_limit,$limit,$fid,$tids );
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
			$threadlist_query = DB::query("SELECT * FROM ".DB::table('forum_thread')." WHERE (stamp=0 or icon=9 or digest in(1,2,3)) AND fid=".$fid." ORDER BY lastpost desc limit $start_limit,$limit");
			while ($threadlist_list = DB::fetch($threadlist_query)) {
				$threadlist[] = $threadlist_list;
			}
			$threadlist_count =  DB::fetch(DB::query("SELECT count(*) as nums FROM ".DB::table('forum_thread')." WHERE (stamp=0 or icon=9 or digest in(1,2,3)) AND fid=".$fid));
			$count=(Int)$threadlist_count[nums];
} else { 
	/*
	$filterbool = true;
	$displayorderadd = !$filterbool && $stickycount ? 't.displayorder IN (0, 1)' : 't.displayorder IN (0, 1, 2, 3, 4)';
	if($_GET ['sortby'] == 'essence')
	{
		if(!empty($digest))
		{
			$digest =" AND (t.digest in (".implode(',',$digest).") or t.icon = 9 or t.stamp = 0)";
		}else{
			$digest =" AND ( t.icon = 9 or t.stamp = 0)";
		}
	}
	else
	{
		$digest = '';
	}
	if(($start_limit && $start_limit > $stickycount) || !$stickycount || $filterbool) {
		$indexadd = '';
		if(strexists($filteradd, "t.digest>'0'")) {
			$indexadd = " FORCE INDEX (digest) ";
		}
		$querysticky = '';
		$query = DB::query("SELECT t.* FROM ".DB::table($threadtable)." t $indexadd
				WHERE $fidsql  AND ($displayorderadd $digest)
				ORDER BY t.displayorder DESC, t.".$isPublish." DESC
				LIMIT ".$start_limit.", $limit");
	} else {
		$querysticky = DB::query("SELECT t.* FROM ".DB::table($threadtable)." t
		WHERE t.tid IN ($stickytids) AND (t.displayorder IN (2, 3, 4))
		ORDER BY ".$isPublish." DESC
		LIMIT $start_limit, ".($stickycount - $start_limit < $_G['tpp'] ? $stickycount - $start_limit : $_G['tpp']));
	
		if($_G['tpp'] - $stickycount + $start_limit > 0) {
			$query = DB::query("SELECT t.* FROM ".DB::table($threadtable)." t
					WHERE $fidsql  AND ($displayorderadd)
					ORDER BY ".$isPublish." DESC
					LIMIT ".($_G['tpp'] - $stickycount + $start_limit));
		} else {
			$query = '';
		}
	}
	while($group = DB::fetch($query)){
		$threadlist[$group['tid']] = $group;
	}*/
	$threadlist_count = DB::fetch(DB::query("SELECT count(*) as nums FROM ".DB::table('forum_thread')." WHERE displayorder=0 AND fid=".$fid));
	$threadlist_query = DB::query("SELECT * FROM ".DB::table('forum_thread')." WHERE fid=".$fid." ORDER BY lastpost desc limit $start_limit,$limit");
	while ($threadlist_list = DB::fetch($threadlist_query)) {
		$threadlist[] = $threadlist_list;
	}
	$count=(Int)$threadlist_count[nums];
}
//print_r($threadlist);exit;

if($page ==1 && empty($_GET ['sortby']))
{
	$threadlist =empty($threadlist)?$threadlist_top:array_merge($threadlist_top,$threadlist);
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
	$query = DB::query("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$group['tid']." AND p.first =1");
	while ($rows = DB::fetch($query)) {
		$ForumImg = $rows;
	}
	if ($ForumImg ["attachment"] == 2) {
		$pic = C::t('forum_thread') ->fetch_all_threadimage($group);
		if(!empty($pic)){
			$filename = $picNew->parseTradeTopicImg($pic);
		}
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
			$topicInstance = new topic();
			$trades['thumb'] = str_replace('forum/', '', $trades['thumb']);
			$filename = $topicInstance ->parseTradeTopicImg($trades);
		}
	}
	$data ['board_id'] = ( int ) $group ['fid']; 
	$data ['topic_id'] = ( int ) $group ['tid']; 
	$data ['type_id'] = ( int ) $group ['typeid'];
	$data ['sort_id'] = ( int ) $group ['sortid'];
	if($group ["special"]==1){
		$data ['vote'] = (int)1;
	}else{
		$data ['vote'] = (int)0;
	}
	$data ['title'] = sub_str($group ['subject'], 0,40);
	$data ['subject'] = $group ['message'];
	$data ['user_id'] = ( int ) $group ['authorid']; 
	$data ['last_reply_date'] = ($group ['lastpost']) . "000"; 
	if(empty($group ['author']))
	{
		$data ['user_nick_name'] =Common::get_unicode_charset('\u533f\u540d');;
	}
	else
	{
		$data ['user_nick_name'] = $group ['author']; 
	}
	$data ['hits'] = ( int ) $group ['views']; 
	$data ['replies'] = ( int ) $group ['replies']; 
	$data ['status'] = ( int ) $group ['status']; 
	$data ['essence'] = ( int ) $group ['digest'] >0|| ( int ) $group ['icon'] ==9 || (int ) $group ['stamp'] ==0? 1 : 0; 
	$data ['top'] = ( int ) $group ['displayorder'] >0|| ( int ) $group ['icon'] ==13 || (int ) $group ['stamp'] ==4? 1 : 0;
	                                                      
	                                                      
	$data ['hot'] = ( int ) $group ['stamp'] ==1|| ( int ) $group ['icon'] ==10? 1 : 0;
    if ($filename) {
				$data ['pic_path'] = $filename;
			}else {
				$data ['pic_path'] = '';
			}
	unset ( $filename );
	$ret_pic_path = '';
	$data_thread [] = $data;
} 
if(empty($_GET ['digest'])&& empty($_GET ['displayorder']) && empty($_GET ['sortby']) && $page ==1)
{
	$announcement = C::t ( 'forum_announcement' )->fetch_all_by_displayorder ();
	while ($announ = DB::fetch($announcement)) {
		$data_announ [] = array (
				"announce_id" => $announ ["id"],
				"author" => $announ ["author"],
				"board_id" => '',
				"forum_id" => '',
				"start_date" => $announ ["starttime"] . '000',
				"title" => preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$announ ["subject"])
		);
	}
}

/*[fen lei ming cheng]*/

$forum=	DB::fetch_first("SELECT ff.*, f.* FROM ".DB::table('forum_forum')." f LEFT JOIN ".DB::table('forum_forumfield')." ff ON ff.fid=f.fid WHERE f.fid=".$_GET['boardId']);
$isfenleishow_arr=unserialize($forum[threadsorts]);
$isleibieshow_arr=unserialize($forum[threadtypes]);
$isfenleishow=$isfenleishow_arr[prefix];
$isleibieshow=$isleibieshow_arr[prefix];

$topicInstance = new topic();
if($isfenleishow==1){
	for($i=0;$i<count($data_thread);$i++){
		if($data_thread[$i][sort_id]!=0){
			$fenlei_name= C::t ( 'forum_threadtype' )->fetch_name_by_typeid ($data_thread[$i][sort_id]);
			foreach($fenlei_name as $fl){
				$fenleimingcheng=$topicInstance->replaceHtmlAndJs($fl);
				$data_thread[$i][title]="[".$fenleimingcheng."]".$topicInstance->replaceHtmlAndJs($data_thread[$i][title]);
			}
		}
	}
}

if($isleibieshow==1 || $isleibieshow==2){
	for($i=0;$i<count($data_thread);$i++){
		if($data_thread[$i][sort_id]!=0){
			$fenlei_type= C::t ( 'forum_threadclass' )->fetch_all_by_typeid ($data_thread[$i][type_id]);
			foreach($fenlei_type as $ftype){
				$fenleitypemingcheng=$topicInstance->replaceHtmlAndJs($ftype);
				$data_thread[$i][title]="[".$fenleitypemingcheng."]".$data_thread[$i][title];
			}
		}
	}
}

/*[end fen lei ming cheng]*/
 
if ($stamp) {
	$digest =" AND (t.icon = 10 or t.stamp = 1)";
	$count = C::t('forum_thread') ->count_hot_search($stamp = " and t.stamp='".$stamp."'".$digest,$displayorder,$fid,$tids);
	
}elseif ($zhiding && ! empty ( $fid )) {
			$count = $zd_count;
} else {
	if($_GET ['sortby'] == 'essence' && !empty($digest))
	{
			$digest = $digest;
	}
	else
	{
		$digest = '';
	}
	//$count = C::t('forum_thread') ->count_hot_search($stamp =$digest,$displayorder,$fid,$fid);

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