<?php
require_once './abstractUserInfo.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../model/table/x25/topic.php';
require_once '../Config/public.php';
require_once '../public/mobcentDatabase.php';
require_once '../model/table/x25/table_common_member.php';
require_once '../../source/function/function_forum.php';
define('IN_MOBCENT',1);
define('ALLOWGUEST', 1);
C::app ()->init();
require_once '../model/table/x25/topic.php';
require_once '../tool/Thumbnail.php';
require_once libfile ( 'function/discuzcode' );

class userInfoImpl_x25 extends abstractUserInfo {
	public function getUserInfoObj()
	{
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
		$space = $info->getUserInfo ( intval ($arrAccess['user_id']) );
		
		if(empty($arrAccess))
		{
			return $info -> userAccessError();
			exit();
		}
		else
		{
			$uid =$_G ['uid']= empty($_GET ['userId'])?$arrAccess['user_id']:$arrAccess['user_id'] == $_GET ['userId']? intval($arrAccess['user_id']):intval ( $_REQUEST ['userId']);  
			if(empty($uid))
			{
				return $info -> userAccessError();
				exit();
			}
		}
		$smile_query = DB::query("SELECT code FROM ".DB::table('common_smiley')." WHERE type = 'smiley'");
		while ($smile_list = DB::fetch($smile_query)) {
			$smile_arr[] = $smile_list;
		}
		foreach($smile_arr as $sr){
			$smiles[]=$sr[code];
		}
		if ((isset($_GET ['userId']) && !empty($_GET ['userId'])) || (isset($_GET ['accessSecret']) && !empty($_GET ['accessSecret'])) ) {
			$followes = C::t ( 'home_follow' )->fetch_all_by_uid_followuid ( $arrAccess['user_id'], $uid );
			if (count ( $followes )) {
				$followe = 1;
			}
			$blackfriend = DB::fetch_first ( 'SELECT uid FROM %t WHERE  buid=%d AND uid = %d limit 1', array (
					'home_blacklist',
					$uid,
					$arrAccess['user_id']
			) );
			if ($blackfriend ['uid']) {
				$is_black = 1;
			}
		
		
			$follow_num = DB::fetch_first ( "SELECT count(*) as count FROM %t WHERE followuid=%d", array (
					'home_follow',
					$uid
			) );
			$friend_num = DB::fetch_first ( "SELECT count(*) as count FROM %t WHERE uid=%d", array (
					'home_follow',
					$uid
			) );
			$status = DB::fetch_first ( "SELECT status FROM %t WHERE uid=%d", array (
					'common_member',
					$uid
			) );
			$status = DB::fetch_first ( "SELECT uid,invisible as status FROM ".DB::table('common_session')." WHERE uid=".$uid );
			if(empty($status))
			{
				$online = 0;
			}
			else
			{
				$online = empty($status ['status'])?1:0;
			}
			
			if(empty($status ['uid']) && $arrAccess['user_id'] == $_GET ['userId'])
			{
				$ip = get_client_ip();
				$info = new mobcentGetInfo();
				$UserInfo = $info->getUserInfo(intval($uid));
				$ip_array = explode('.', $ip);
				$sid = Common::randomkeys(6);
				$info = array(
						'sid' => $sid,
						'ip1' => $ip_array[0],
						'ip2' => $ip_array[1],
						'ip3' => $ip_array[2],
						'ip4' => $ip_array[3],
						'uid' => $UserInfo['uid'],
						'username' =>$UserInfo['username'],
						'groupid' => $UserInfo['groupid'],
						'invisible' =>'0',
						'action' => 'APPTYPEID' ,
						'lastactivity' => time(),
						'fid' => '0',
						'tid' => '0',
						'lastolupdate' => '0'
				);
				DB::insert('common_session',$info);
			}
			$space = getuserbyuid ( $uid, 1 ); 
			if ($space ['uid'] == '') {
				echo '{"rs":0,"errcode":"010000001"}';
				exit ();
			}
			
			/*rx userinfo 20130905*/
			$setting_list = DB::query("SELECT * FROM ".DB::table('common_setting'));
			while($setting_value = DB::fetch($setting_list)) {
				$setting[] = $setting_value;
			}
			foreach($setting as $st){
				if($st[skey]=='ftp'){
					$myval=unserialize($st[svalue]);
					$ftp_isopen=$myval[on];
					$ftp_host=$myval[host];
					$ftp_attachurl=$myval[attachurl];
				}
			}
			
			if($_GET ['userId']==$arrAccess['user_id']){
				$favourite_query=DB::query("SELECT a.*,b.* FROM ".DB::table('home_favorite')." a,".DB::table('forum_thread')." b where a.id=b.tid and a.uid = ".$_GET ['userId']." order by a.id desc limit 0,5");
				while($favourite_list=DB::fetch($favourite_query)){
					$favourite_arr[]=$favourite_list;
				} 
				foreach($favourite_arr as $thread){
					$First_querya=DB::fetch(DB::query("SELECT * FROM ".DB::table('forum_post')." WHERE tid=".(Int)$thread ['tid']." AND first=1 "));
					$board_name=DB::fetch(DB::query("SELECT name FROM ".DB::table('forum_forum')." WHERE fid=".(Int)$thread ['fid']));
					$data ['board_id'] = ( int ) $thread ['fid'];
					$data ['board_name'] = $board_name[name];
					$data ['topic_id'] = ( int ) $thread ['tid'];
					$data ['title'] = $thread['subject'];
					$data ['user_id'] = ( int ) $thread ['authorid'];
					$data ['lastpost'] = ($thread ['lastpost']) . "000";
					$data ['user_nick_name'] = $thread['author'];
					$data ['hits'] = ( int ) $thread ['views'];
					preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i",  $First_querya ['message'] ,$matches);
					$patten  = array("\r\n", "\n", "\r");
					$data ['content'] =str_replace($matches[1], '', $First_querya ['message']);
					$data ['content'] =str_replace($patten, '', $data ['content']);
					$data ['content'] = preg_replace("#(\w*)\[.*?\](\w*)#","$1$2",$data ['content']);
					foreach($smiles as $si){
						$data ['content'] =str_replace($si, '', $data ['content']);
					}
					$data ['content'] =trim($data ['content']);
					$data ['content'] = sub_str($data ['content'], 0,40);
					$data ['replies'] = ( int ) $thread ['replies'];
					$topicInstance = new topic();
					$query = DB::query ( "SELECT B.*,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $thread['tid']);
					$pic = DB::fetch($query);
					if(!empty($pic)){
						$data ['pic_path'] = $topicInstance->parseTargeImage($pic);
					}else{
						$data ['pic_path'] = "";
					}
					$itemData[]=$data;
				}
				$user_data ['flag'] = 1;
			}else{
				$topic_query=DB::query("SELECT * FROM ".DB::table('forum_thread')." where authorid = ".$uid." AND displayorder != -1 order by lastpost desc limit 0,5");
				while($topic_list=DB::fetch($topic_query)){
					$topic_arr[]=$topic_list;
				}
				foreach($topic_arr as $thread){
					$First_querya=DB::fetch(DB::query("SELECT * FROM ".DB::table('forum_post')." WHERE tid=".(Int)$thread ['tid']." AND first=1 "));
					$data ['board_id'] = ( int ) $thread ['fid'];
					$data ['topic_id'] = ( int ) $thread ['tid'];
					$data ['title'] = $thread['subject'];
					$data ['user_id'] = ( int ) $thread ['authorid'];
					$data ['lastpost'] = ($thread ['lastpost']) . "000";
					$data ['user_nick_name'] = $thread['author'];
					$data ['hits'] = ( int ) $thread ['views'];
					preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i",  $First_querya ['message'] ,$matches);
					$patten  = array("\r\n", "\n", "\r");
					$data ['content'] =str_replace($matches[1], '', $First_querya ['message']);
					$data ['content'] =str_replace($patten, '', $data ['content']);
					$data ['content'] = preg_replace("#(\w*)\[.*?\](\w*)#","$1$2",$data ['content']);
					foreach($smiles as $si){
						$data ['content'] =str_replace($si, '', $data ['content']);
					}
					$data ['content'] =trim($data ['content']);
					$data ['content'] = sub_str($data ['content'], 0,40);
					$data ['replies'] = ( int ) $thread ['replies'];
					$topicInstance = new topic();
					$query = DB::query ( "SELECT B.*,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $thread['tid']);
					$pic = DB::fetch($query);
					if(!empty($pic)){
						$data ['pic_path'] = $topicInstance->parseTargeImage($pic);
					}else{
						$data ['pic_path'] = "";
					}
					$itemData[]=$data;
				}
				$user_data ['flag'] = 0;
			}
			
			/*end rx*/
			$list_count=DB::fetch(DB::query("SELECT count(*) as nums FROM ".DB::table('home_album')." where uid = ".$uid));
			$count = C::t ( 'forum_thread' )->count_by_special ( '', $uid );
			$space_pro=DB::fetch(DB::query("SELECT * FROM ".DB::table('common_member_profile')." where uid = ".$uid));
			$space_count=DB::fetch(DB::query("SELECT * FROM ".DB::table('common_member_count')." where uid = ".$uid));
			$space = array_merge ( $space, $space_pro,$space_count);
				
			$user_data ['is_black'] = ( int ) $is_black;  
			$user_data ['is_follow'] = ( int ) $followe; 
			$user_data ['icon_url'] = DISCUZSERVERURL;
			$user_data ['icon'] = userIconImg ( $uid ); 
			$user_data ['level_url'] = '';
			$user_data ['name'] = $space ['username'];
			$user_data ['email'] = $space ['email'];
			$user_data ['status'] = ( int )$online;
			$user_data ['gender'] = ( int ) $space ['gender'];
			$user_data ['email'] = $space ['email'];
			$user_data ['score'] = ( int )$space ['credits'];
			$user_data ['credits'] = ( int )$space ['credits'];  
			$user_data ['gold_num'] = ( int ) $space ['extcredits2'];  
			$user_data ['topic_num'] = ( int ) $count; 
			$user_data ['photoNum'] = ( int ) $list_count[nums];
			$user_data ['reply_posts_num'] = ( int ) ($space ['posts'] - $space ['threads']);
			$user_data ['essence_num'] = ( int ) $space ['digestposts'];
			$user_data ['follow_num'] = ( int )$follow_num ['count'];  
			$user_data ['friend_num'] = ( int )$friend_num ['count'];  
			$user_data ['rs'] = 1;
			$user_data ['relational_num'] = 4;
			$space['groupid'];
			$groups = DB::fetch_first('select * from %t where groupid=%d limit 1',array('common_usergroup',$space['groupid']));
			$user_data ['level'] =(int)$groups['stars'];  
			$user_data ['info'] = empty($itemData)?array():$itemData;
		}else{
			$user_data['rs'] = 0;
			$user_data['errcode'] = "01000000";
		}
		return $user_data;
	}
}

?>