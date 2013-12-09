<?php
define('IN_MOBCENT',1);
require_once '../boardlist/abstarctBoardList.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once libfile('function/forumlist');
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../model/table_common_member_profile.php';
require_once '../public/mobcentDatabase.php';

class boardListImpl_x25_gbk extends abstarctBoardList {
	public function getBoardListObj() {
		define('ALLOWGUEST', 1);
		C::app ()->init();
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
		 $space = $info->getUserInfo(intval($_GET['accessSecret']));
		 $_G=array_merge($_G,$space);
		 
		
		
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
						if(forum_2($forum)) {
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
		
				} else {
		
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
		foreach($forumlist as $k=>$forum){
			$data_forum[$forum["fup"]][]=array(
					"board_id"			=>(int)$forum["fid"], 		 
					"board_name"		=>$forum["name"],		 
					"td_posts_num"		=>(int)$forum["todayposts"],	 
					"topic_total_num"	=>(int)$forum["threads"],	 
					"posts_total_num"	=>(int)$forum["posts"],		 
					"last_posts_date"	=> $forum["lastpost"]['dateline'] = '0' ?'0' :$forum["lastpost"]['dateline'].'000',	 
			);
		}
		foreach($catlist as $k=>$cat){
			if(strstr(MOBCENBTYPE2,"|".$cat["fid"]."|")==""){
				$type=2;
			}else{
				$type=1;
			}
			$data_cat[]=array(
				"board_category_id"		=>(int)$cat["fid"],  
				"board_category_name"	=>$cat["name"],	 
				"board_category_type"   =>$type,	 
				"board_list"			=>$data_forum[$cat["fid"]],	 
			);
		}	
		$number_obj = C::app()->session;
		$numbers=C::app()->session->count();
		$todaytime=strtotime(date("Y-m-d"));
		$tomorrowtime=strtotime(date("Y-m-d",strtotime("+1 day")));
		$N=DB::result_first('SELECT COUNT(*) FROM %t where lastolupdate between %s and %s', array('common_session',$todaytime,$tomorrowtime));
		$retarry = array($numbers, $N , $data_cat);
		return $retarry;
			}
		}

?>