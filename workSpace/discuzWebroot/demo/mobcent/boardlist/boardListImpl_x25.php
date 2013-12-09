<?php 
define('IN_MOBCENT',1);
require_once '../../source/class/class_core.php';
require_once 'boardListImpl_x25_gbk.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once libfile('function/forumlist');
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../model/table_common_member_profile.php';
require_once '../model/table/x25/topic.php';
require_once '../public/mobcentDatabase.php';
require_once '../app/config/constant.php';
require_once ('./abstarctBoardList.php');
class boardListImpl_x25 extends abstarctBoardList {
	public function getBoardListObj() {
		define('ALLOWGUEST', 1);
		C::app ()->init ();
		global $_G;
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
		$space = $info->getUserInfo(intval($arrAccess['user_id']));
		$_G=array_merge($_G,$space); 
		//if(empty($_GET['boardLineNum'])) $boardLineNum = 2;
		$_G['groupid'] =$group['groupid'];
		$gid = intval(getgpc('gid'));
		$catlist = $forumlist = $sublist = $forumname = $collapse = $favforumlist = array();
		if(!$gid) {
			$forums = C::t('forum_forum')->fetch_all_by_status(1);
			$fids = array();
			foreach($forums as $forum) {
				$fids[$forum['fid']] = $forum['fid'];
			}
			$forum_access = array();
			if(!empty($_G['member']['accessmasks'])) {
				$forum_access = C::t('forum_access')->fetch_all_by_fid_uid($fids, $_G['uid']);
			}
			$forum_fields = C::t('forum_forumfield')->fetch_all($fids);
			foreach($forums as $forum) {
				if($forum_fields[$forum['fid']]['fid']) {
					$forum = array_merge($forum, $forum_fields[$forum['fid']]);
				}
				if($forum_access['fid']) {
					$forum = array_merge($forum, $forum_access[$forum['fid']]);
				}
				$forumname[$forum['fid']] = strip_tags($forum['name']);
				$forum['extra'] = empty($forum['extra']) ? array() : dunserialize($forum['extra']);
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
					} elseif(isset($forumlist[$forum['fup']])) {
						$forumlist[$forum['fup']]['threads'] += $forum['threads'];
						$forumlist[$forum['fup']]['posts'] += $forum['posts'];
						$forumlist[$forum['fup']]['todayposts'] += $forum['todayposts'];
						if($_G['setting']['subforumsindex'] && $forumlist[$forum['fup']]['permission'] == 2 && !($forumlist[$forum['fup']]['simple'] & 16) || ($forumlist[$forum['fup']]['simple'] & 8)) {
							$forumurl = !empty($forum['domain']) && !empty($_G['setting']['domain']['root']['forum']) ? 'http://'.$forum['domain'].'.'.$_G['setting']['domain']['root']['forum'] : 'forum.php?mod=forumdisplay&fid='.$forum['fid'];
							$forumlist[$forum['fup']]['subforums'] .= (empty($forumlist[$forum['fup']]['subforums']) ? '' : ', ').'<a href="'.$forumurl.'" '.(!empty($forum['extra']['namecolor']) ? ' style="color: ' . $forum['extra']['namecolor'].';"' : '') . '>'.$forum['name'].'</a>';
						}
					}
				}else{
					if($forum['moderators']) {
						$forum['moderators'] = moddisplay($forum['moderators'], 'flat');
					}
					$forum['forumscount'] 	= 0;
					$catlist[$forum['fid']] = $forum;
				}
			}
		}else {
			$gquery = C::t('forum_forum')->fetch_all_info_by_fids($gid);
			$query = C::t('forum_forum')->fetch_all_info_by_fids(0, 1, 0, $gid, 1, 0, 0, 'forum');
			if(!empty($_G['member']['accessmasks'])) {
				$fids = array_keys($query);
				$accesslist = C::t('forum_access')->fetch_all_by_fid_uid($fids, $_G['uid']);
				foreach($query as $key => $val) {
					$query[$key]['allowview'] = $accesslist[$key];
				}
			}
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
					if(forum($forum)) {
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
			unset($forum_access, $forum_fields);
			if($catlist) {
				foreach($catlist as $key => $var) {
					$catlist[$key]['forumcolumns'] = $var['catforumcolumns'];
					if($var['forumscount'] && $var['catforumcolumns']) {
						$catlist[$key]['forumcolwidth'] = (floor(100 / $var['catforumcolumns']) - 0.1).'%';
						$catlist[$key]['endrows'] = '';
						if($colspan = $var['forumscount'] % $var['catforumcolumns']) {
							while(($var['catforumcolumns'] - $colspan) > 0) {
								$catlist[$key]['endrows'] .= '<td>&nbsp;</td>';
								$colspan ++;
							}
							$catlist[$key]['endrows'] .= '</tr>';
						}
					}
				}
				unset($catid, $category);
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
		}
		unset($forum_access, $forum_fields);
		$xm=new topic();
		$s=file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
		$result =$xm->xml_to_array($s);
		if(count($result[board]['fid']) ==1){
			$arr[]=$result[board]['fid'][0];
		}else{
			foreach($result[board]['fid'] as $key =>$val)
			{
				$arr[] =$val[0];
			}
		}
		
		if(count($result[board]['fup']) ==1){
			$arrFup[]=$result[board]['fup'][0];
		}else{
			foreach($result[board]['fup'] as $key =>$val)
			{
				$arrFup[] =$val[0];
			}
		}//print_r($arrFup);exit;
		$partimg ="<img[\\s\\S]+?src=\"(?<src>.*?)\"[\\s\\S]*?/>";
		 
		
		
		if(count($result[pboard]['pfid']) ==1){
			$parr[]=$result[pboard]['pfid'][0];
		}else{
			foreach($result[pboard]['pfid'] as $key =>$val)
			{
				$parr[] =$val[0];
			}
		}
// 		foreach($parr as $pk=>$pv){
// 			$sarr[$pv]=$pv;
// 		}
		//print_r($parr);exit;
		
		foreach($forumlist as $k=>$forum){ 
			$fups = DB::fetch(DB::query("SELECT count(fid) as fs FROM ".DB::table('forum_forum')." where fup=".$forum["fid"]));
			$fcontents = DB::fetch(DB::query("SELECT count(tid) as ts FROM ".DB::table('forum_thread')." where displayorder >'-1' AND fid=".$forum["fid"]));
			//print_r($fcontents[ts]); echo '@';
			$part = "/.*?<span.*?title=\"(.*?)\"/i";
			$res=preg_match_all($partimg,  $forum["icon"],$imgs);
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
				if(in_array($forum["fid"],$parr) && !empty($matches[2][0])){ 
					$pic_match = substr($matches[2][0],0,7)=="http://"?$matches[2][0]:'/'.$matches[2][0];
				}else{
					$pic_match = '';
				} 
				if(in_array($forum["fid"],$arr)){
					$data_forum[$forum["fup"]][]=array(
							"board_id"			=>(int)$forum["fid"],
							"board_name"		=>preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$forum["name"]),
							"board_child"		=>$fups[fs]==0?0:1,
							"board_img"			=>$pic_match,
							"board_content"		=>$fcontents[ts]==0?0:1,
							"td_posts_num"		=>(int)$forum["todayposts"],
							"topic_total_num"	=>(int)$forum["threads"],
							"posts_total_num"	=>(int)$forum["posts"],
							"last_posts_date"	=> strtotime($match[1][0]).'000',
							//'pic_path'  =>empty($imgs['src'][0])?'':$imgs['src'][0] 
					);
				}
			}else{
				if(!empty($matches[2][0])){
					$pic_match = substr($matches[2][0],0,7)=="http://"?$matches[2][0]:'/'.$matches[2][0];
				}else{
					$pic_match = '';
				}
				$data_forum[$forum["fup"]][]=array(
						"board_id"			=>(int)$forum["fid"],
						"board_name"		=>preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$forum["name"]),
						"board_child"		=>$fups[fs]==0?0:1,
						"board_img"			=>$pic_match,
						"board_content"		=>$fcontents[ts]==0?0:1,
						"td_posts_num"		=>(int)$forum["todayposts"],
						"topic_total_num"	=>(int)$forum["threads"],
						"posts_total_num"	=>(int)$forum["posts"],
						"last_posts_date"	=> strtotime($match[1][0]).'000',
				);
			}
		}
		$topicInstance = new topic();
		$rst = array();
		foreach($result[category] as $rk=>$rc){
			foreach ($rc as $keys => $value) {
				$rst[$keys][$rk] = $value[0];
			}
		}
		foreach($rst as $rt){
			$arrs[$rt[cid]]=$rt;
		}
		foreach($catlist as $ks=>$vs){
			if($arrs[$ks][cid]==""){
				$arrs[$ks][cid]=$catlist[$ks][fid];
				$arrs[$ks][cname]=$catlist[$ks][name];
				$arrs[$ks][ctype]=2; // default: shuang lie
			}
		}
		foreach($catlist as $ks=>$vs){
			if($arrs[$ks][cid]!=""){
				$catlistArr[]=array_merge($arrs[$ks],$catlist[$ks]);
			}
		}
		//print_r($catlistArr);exit;
		foreach($catlistArr as $k=>$cat){ 
			if(!empty($result)){
				if(in_array($cat["fid"], $arrFup)){
					$data_cat[]=array(
							"board_category_id"		=>(int)$cat["fid"],
							"board_category_name"	=>$topicInstance->replaceHtmlAndJs($cat["name"]),
							"board_category_type"   =>(int)$cat["ctype"],
							"board_list"			=>empty($data_forum[$cat["fid"]])?array():$data_forum[$cat["fid"]],
					);
				}
			} else{
				$data_cat[]=array(
						"board_category_id"		=>(int)$cat["fid"],
						"board_category_name"	=>$topicInstance->replaceHtmlAndJs($cat["name"]),
						"board_category_type"   =>(int)$cat["ctype"],
						"board_list"			=>empty($data_forum[$cat["fid"]])?array():$data_forum[$cat["fid"]],
				);
			}
		} 
		$number_obj = C::app()->session;
		$numbers=C::app()->session->count();
		$todaytime=strtotime(date("Y-m-d"));
		$tomorrowtime=strtotime(date("Y-m-d",strtotime("+1 day")));
		$N=DB::result_first('SELECT COUNT(*) FROM %t where lastolupdate between %s and %s', array('common_session',$todaytime,$tomorrowtime));
	    $data = array($numbers,$N,$data_cat);
	    $version = MOBCENT_RELEASE;
    	$info = array(
    				'online_user_num'	=>$data[0],
    				'td_visitors'		=>$data[1],
    				'list'				=>$data[2],
    				"img_url"			=>"",
    				"version"			=>$version,
    				"rs"				=>(Int)1
    	);    	
		return $info;
}}

?>