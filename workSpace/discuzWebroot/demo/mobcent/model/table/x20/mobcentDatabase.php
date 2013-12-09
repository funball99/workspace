<?php
	class mobcentGetInfo
	{
		public function forum_check_content($uid,$topicInstance)
		{
			$url=file_exists('../manage/App.xml')?join("",file('../manage/App.xml')):array();
			$result =$topicInstance->xml_to_array($url);
			
			if(empty($result['board']['fid']))
			{
				$sql = !empty($_G['member']['accessmasks']) ?
				"SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.inheritedmod, f.domain,
						f.forumcolumns, f.simple, ff.description, ff.moderators, ff.icon, ff.viewperm, ff.redirect, ff.extra, a.allowview
						FROM ".DB::table('forum_forum')." f
						LEFT JOIN ".DB::table('forum_forumfield')." ff ON ff.fid=f.fid
						LEFT JOIN ".DB::table('forum_access')." a ON a.uid='$uid' AND a.fid=f.fid
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
					foreach($forumlist as $key =>$val)
					{
						$tids[]=$key;
					}
					
					$tids = implode(',', $tids);
				}else{
					if(count($result[board]['fid']) ==1){
						$arr[]=$result[board]['fid'][0];
					}else{
						foreach($result[board]['fid'] as $key =>$val)
						{
							$arr[] =$val[0][0];
						}
					}
					$tids =implode(',', $arr);
					$tids = rtrim($tids,',');
					$tids = rtrim($tids,',');
				}
				return $tids;
		}
		public function rank_check_allow($accessSecret,$accessToken,$qquser)
		{
			if(empty($accessSecret) || empty($accessToken))
			{
				$query = $this-> sel_QQuser($qquser);
				while($arr = DB::fetch($query))
				{
					$group =$arr;
				}
			}
			else
			{
				$arrAccess = $this->sel_accessTopkent($accessSecret,$accessToken);
				$userId = $arrAccess['user_id'];
				if(empty($userId))
				{
					return $this -> userAccessError();
					exit();
				}
				$group = $this-> sel_group_by_uid_allow($userId);
			}
			return $group;
		}
		
		public function search_check_allow($accessSecret,$accessToken,$qquser)
		{
			if(empty($accessSecret) || empty($accessToken))
			{ 
				$query = DB::query('SELECT a.*,b.* FROM '.DB::table('common_usergroup').' as a LEFT JOIN '.DB::table('common_usergroup_field').' as b on a.groupid = b.groupid WHERE a.groupid = 7');
				while($arr = DB::fetch($query))
				{
					$group =$arr;
				}
			}
			else
			{
				$arrAccess = $this->sel_accessTopkent($accessSecret,$accessToken);
				$userId = $arrAccess['user_id'];
				if(empty($userId))
				{
					return $this -> userAccessError();
					exit();
				}
				$group = $this-> rx_group_by_uid_allow($userId);
			} 
			return $group;
		}
		
		
		public function userAccessError()
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = 50000000;
			return $data_post;
		}
		public function rx_QQuser($qquser)
		{
			$query = DB::fetch_first('SELECT a.*,b.* FROM '.DB::table('common_usergroup').' as a LEFT JOIN '.DB::table('common_usergroup_field').' as b on a.groupid = b.groupid WHERE a.groupid = 7');
			return $query;
		}
		public function rx_group_by_uid_allow($user)
		{
			$query = DB::fetch_first('SELECT b.*,c.* FROM  '.DB::table('common_member').' as a LEFT JOIN '.DB::table('common_usergroup').' as b ON a.groupid = b.groupid LEFT JOIN '.DB::table('common_usergroup_field').' as c ON a.groupid = c.groupid WHERE a.uid = '.$user);
			return $query;
		}
		
		public function sel_QQuser($qquser)
		{
			$query = DB::query("SELECT b.readaccess,b.allowpost,b.allowreply,a.allowvisit,a.groupid,b.allowpostpoll FROM ".DB::table('common_usergroup')." as a LEFT JOIN ".DB::table('common_usergroup_field')." as b on a.groupid = b.groupid WHERE a.groupid = 7");
			return $query;
		}
		public function sel_group_by_uid_allow($user)
		{
			$query = DB::fetch_first('SELECT b.allowvisit, b.groupid FROM  '.DB::table('common_member').' as a LEFT JOIN '.DB::table('common_usergroup').' as b on a.groupid = b.groupid WHERE a.uid = '.$user);
			return $query;
		}
		
		
		public function sel_group_by_uid($user)
		{
			$query = DB::fetch_first('SELECT b.readaccess, b.groupid,b.allowpost,b.allowreply,b.allowpostpoll FROM  '.DB::table('common_member').' as a LEFT JOIN '.DB::table('common_usergroup_field').' as b on a.groupid = b.groupid WHERE a.uid = '.$user);
		    return $query;
		}
		public function sel_accessTopkent($accessSecret,$accessToken)
		{
			$query = DB::fetch_first("SELECT user_id FROM ".DB::table('home_access')." WHERE user_access_token ='".$accessToken."' AND user_access_secret = '".$accessSecret."'");
			return $query;
		}
		public function getUserInfo($uid){
			return getuserbyuid($uid,1);
		}
		public function getBoard($boardId){
			return $board = DB::fetch_first("SELECT modnewposts,type FROM ".DB::table('forum_forum')." WHERE fid= ".$boardId);
			if($board['type']!='group'){
				return false;
			}else{
				return $board;
			}
		}
		public function getForumSub($SubId){
			return $board = DB::fetch_first("SELECT modnewposts,type FROM ".DB::table('forum_forum')." WHERE fid= ".$SubId);
			if($board['type']!='sub'){
				return false;
			}else{
				return $board;
			}
		}
	}
?>
