<?php
require_once '../model/class_core.php';
require_once '../boardlist/abstarctBoardList.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../../source/function/function_forumlist.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../model/table/x20/topic.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
define('IN_MOBCENT',1);
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/mobcentDatabase.php';
class boardListImpl_x20 extends abstarctBoardList {
	public function getBoardListObj() {
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
		setglobal('groupid', $group['groupid']);
		global $_G;
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		 $space = $info->getUserInfo(intval(($arrAccess['user_id'])));
		$gid = intval(getgpc('gid'));
		$catlist = $forumlist = $sublist = $forumname = $collapse = $favforumlist = array();
		if(!$gid) {
			$sql = !empty($_G['member']['accessmasks']) ?
			"SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.inheritedmod, f.domain,
					f.forumcolumns, f.simple, ff.description, ff.moderators, ff.icon, ff.viewperm, ff.redirect, ff.extra, a.allowview
					FROM ".DB::table('forum_forum')." f
					LEFT JOIN ".DB::table('forum_forumfield')." ff ON ff.fid=f.fid
					LEFT JOIN ".DB::table('forum_access')." a ON a.uid='$_G[uid]' AND a.fid=f.fid
						WHERE f.status='1' ORDER BY f.type, f.displayorder"
						: "SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.inheritedmod, f.domain,
					f.forumcolumns, f.simple, ff.description, ff.moderators, ff.icon, ff.viewperm, ff.redirect, ff.extra
					FROM ".DB::table('forum_forum')." f
					LEFT JOIN ".DB::table('forum_forumfield')." ff USING(fid)
					WHERE f.status='1' ORDER BY f.type, f.displayorder";
			
			$query = DB::query($sql);
			while($forum = DB::fetch($query)) {
				
				$forumname[$forum['fid']] = strip_tags($forum['name']);
				$forum['extra'] = unserialize($forum['extra']);
				if(!is_array($forum['extra'])) {
					$forum['extra'] = array();
				}
			
				if($forum['type'] != 'group') {
			
					$threads += $forum['threads'];
					$posts += $forum['posts'];
					$todayposts += $forum['todayposts'];
			
					if($forum['type'] == 'forum' && isset($catlist[$forum['fup']])) {
						if(forum($forum)) {
							$catlist[$forum['fup']]['forums'][] = $forum['fid'];
							$forum['orderid'] = $catlist[$forum['fup']]['forumscount']++;
							$forum['subforums'] = '';
							$forumlist[$forum['fid']] = $forum;
						}
						$arr[]=$forumlist;
					} elseif(isset($forumlist[$forum['fup']])) {
			
						$forumlist[$forum['fup']]['threads'] += $forum['threads'];
						$forumlist[$forum['fup']]['posts'] += $forum['posts'];
						$forumlist[$forum['fup']]['todayposts'] += $forum['todayposts'];
						if($_G['setting']['subforumsindex'] && $forumlist[$forum['fup']]['permission'] == 2 && !($forumlist[$forum['fup']]['simple'] & 16) || ($forumlist[$forum['fup']]['simple'] & 8)) {
							$forumurl = !empty($forum['domain']) && !empty($_G['setting']['domain']['root']['forum']) ? 'http://'.$forum['domain'].'.'.$_G['setting']['domain']['root']['forum'] : 'forum.php?mod=forumdisplay&fid='.$forum['fid'];
							$forumlist[$forum['fup']]['subforums'] .= (empty($forumlist[$forum['fup']]['subforums']) ? '' : ', ').'<a href="'.$forumurl.'" '.(!empty($forum['extra']['namecolor']) ? ' style="color: ' . $forum['extra']['namecolor'].';"' : '') . '>'.$forum['name'].'</a>';
						}
					}
			
				} else {
			
					if($forum['moderators']) {
						$forum['moderators'] = moddisplay($forum['moderators'], 'flat');
					}
					$forum['forumscount'] 	= 0;
					$catlist[$forum['fid']] = $forum;
			
				}
			}
			if(!IS_ROBOT && ($_G['setting']['whosonlinestatus'] == 1 || $_G['setting']['whosonlinestatus'] == 3)) {
				$_G['setting']['whosonlinestatus'] = 1;
			
				$onlineinfo = explode("\t", $_G['cache']['onlinerecord']);
				if(empty($_G['cookie']['onlineusernum'])) {
					$onlinenum = DB::result_first("SELECT count(*) FROM ".DB::table('common_session'));
					if($onlinenum > $onlineinfo[0]) {
						$onlinerecord = "$onlinenum\t".TIMESTAMP;
						DB::query("UPDATE ".DB::table('common_setting')." SET svalue='$onlinerecord' WHERE skey='onlinerecord'");
						save_syscache('onlinerecord', $onlinerecord);
						$onlineinfo = array($onlinenum, TIMESTAMP);
					}
					dsetcookie('onlineusernum', intval($onlinenum), 300);
				} else {
					$onlinenum = intval($_G['cookie']['onlineusernum']);
				}
				$onlineinfo[1] = dgmdate($onlineinfo[1], 'd');
			
				$detailstatus = $showoldetails == 'yes' || (((!isset($_G['cookie']['onlineindex']) && !$_G['setting']['whosonline_contract']) || $_G['cookie']['onlineindex']) && $onlinenum < 500 && !$showoldetails);
			
				if($detailstatus) {
					$actioncode = lang('action');
			
					$_G['uid'] && updatesession();
					$membercount = $invisiblecount = 0;
					$whosonline = array();
			
					$_G['setting']['maxonlinelist'] = $_G['setting']['maxonlinelist'] ? $_G['setting']['maxonlinelist'] : 500;
					$query = DB::query("SELECT uid, username, groupid, invisible, lastactivity, fid FROM ".DB::table('common_session')." WHERE uid>'0' LIMIT ".$_G['setting']['maxonlinelist']);
					while($online = DB::fetch($query)) {
						$membercount ++;
						if($online['invisible']) {
							$invisiblecount++;
							continue;
						} else {
							$online['icon'] = !empty($_G['cache']['onlinelist'][$online['groupid']]) ? $_G['cache']['onlinelist'][$online['groupid']] : $_G['cache']['onlinelist'][0];
						}
						$online['lastactivity'] = dgmdate($online['lastactivity'], 't');
						$whosonline[] = $online;
					}
					if(isset($_G['cache']['onlinelist'][7]) && $_G['setting']['maxonlinelist'] > $membercount) {
						$query = DB::query("SELECT uid, username, groupid, invisible, lastactivity, fid FROM ".DB::table('common_session')." WHERE uid='0' ORDER BY uid DESC LIMIT ".($_G['setting']['maxonlinelist'] - $membercount));
						while($online = DB::fetch($query)) {
							$online['icon'] = $_G['cache']['onlinelist'][7];
							$online['username'] = $_G['cache']['onlinelist']['guest'];
							$online['lastactivity'] = dgmdate($online['lastactivity'], 't');
							$whosonline[] = $online;
						}
					}
					unset($actioncode, $online);
			
			
					$db = DB::object();
					$db->free_result($query);
					unset($online);
				}
			
			} else {
				$_G['setting']['whosonlinestatus'] = 0;
			}
		
		}	
 
		$xm=new topic();
		$s=file_exists('../manage/App.xml')?join("",file('../manage/App.xml')):array();
		$result =$xm->xml_to_array($s);
		
		if(count($result[board]['fid']) ==1){
			$arrxml[]=$result[board]['fid'][0];
		}else{
			
			foreach($result[board]['fid'] as $key =>$val)
			{
				$arrxml[] =$val[0][0];
			}
			
		}
		if(count($result[board]['fup']) ==1){
			$arrFup[]=$result[board]['fup'][0];
		}else{
			foreach($result[board]['fup'] as $key =>$val)
			{
				$arrFup[] =$val[0][0];
			}
			
		}
		
		foreach($forumlist as $k=>$forum){
			$fups = DB::fetch(DB::query("SELECT count(fid) as fs FROM ".DB::table('forum_forum')." where fup=".$forum["fid"]));
			$fcontents = DB::fetch(DB::query("SELECT count(tid) as ts FROM ".DB::table('forum_thread')." where fid=".$forum["fid"]));
			$part = "/.*?<span.*?title=\"(.*?)\"/i";
			$res=preg_match_all($part, $forum["lastpost"]['dateline'],$match);
			if(empty($match[1][0])){
				$match[1][0] = $forum["lastpost"]['dateline'];
			}
			if(strlen(trim($match[1][0])) < 14){
				$yer = date("Y",time()).'-';
				$match[1][0]=$yer.$match[1][0];
			}
			preg_match_all("/<img(.*)src=\"([^\"]+)\"[^>]+>/isU",$forum["icon"],$matches);
			if(!empty($result))
			{
				if(in_array($forum["fid"], $arrxml)){
				$data_forum[$forum["fup"]][]=array(
						"board_id"			=>(int)$forum["fid"], 
						"board_name"		=>preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$forum["name"]),
						"board_child"		=>$fups[fs]==0?0:1,
						"board_img"			=>empty($matches[2][0])?'':'/'.$matches[2][0],
						"board_content"		=>$fcontents[ts]==0?0:1,
						"td_posts_num"		=>(int)$forum["todayposts"],
						"topic_total_num"	=>(int)$forum["threads"],
						"posts_total_num"	=>(int)$forum["posts"],		
						"last_posts_date"	=> strtotime($match[1][0]).'000',
				);
				}
			}else{
				$data_forum[$forum["fup"]][]=array(
						"board_id"			=>(int)$forum["fid"],
						"board_name"		=>preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$forum["name"]),
						"board_child"		=>$fups[fs]==0?0:1,
						"board_img"			=>empty($matches[2][0])?'':'/'.$matches[2][0],
						"board_content"		=>$fcontents[ts]==0?0:1,
						"td_posts_num"		=>(int)$forum["todayposts"],
						"topic_total_num"	=>(int)$forum["threads"],
						"posts_total_num"	=>(int)$forum["posts"],
						"last_posts_date"	=> strtotime($match[1][0]).'000',
				);
			}
 
		}
		
		$topicInstance = new topic();
		foreach($catlist as $k=>$cat){
			if(!empty($result)){
				if(in_array($cat["fid"], $arrFup)){
					if(strstr(MOBCENBTYPE2,"|".$cat["fid"]."|")==""){
						$type=2;
					}else{
						$type=1;
					}
					$data_cat[]=array(
							"board_category_id"		=>(int)$cat["fid"],
							"board_category_name"	=>$topicInstance->replaceHtmlAndJs($cat["name"]),
							"board_category_type"   =>$type,
							"board_list"			=>empty($data_forum[$cat["fid"]])?array():$data_forum[$cat["fid"]], 
					);
				}
				
			}else{
				if(strstr(MOBCENBTYPE2,"|".$cat["fid"]."|")==""){
					$type=2;
				}else{
					$type=1;
				}
				$data_cat[]=array(
						"board_category_id"		=>(int)$cat["fid"],
						"board_category_name"	=>$topicInstance->replaceHtmlAndJs($cat["name"]),
						"board_category_type"   =>$type,
						"board_list"			=>empty($data_forum[$cat["fid"]])?array():$data_forum[$cat["fid"]],	 
				);
			}
		}	
		$number_obj = $discuz->session;
		$numbers=DB::result_first("SELECT COUNT(*) FROM ".DB::table('common_session')." WHERE invisible = '1'");
		$todaytime=strtotime(date("Y-m-d"));
		$tomorrowtime=strtotime(date("Y-m-d",strtotime("+1 day")));
		$N=DB::result_first('SELECT COUNT(*) FROM '.DB::table('common_session').' where lastolupdate between '.$todaytime.' and '.$tomorrowtime);
		$data = array($numbers,$N,$data_cat);
		$version = file_get_contents('../install/predefined.log');
		$info = array(
				'online_user_num'	=>$data[0],
				'td_visitors'		=>$data[1],
				'list'				=>$data[2],
				"img_url"			=>"",
				"version"			=>$version,
				"rs"				=>(Int)1
		);
		return $info;
		}

}



?>