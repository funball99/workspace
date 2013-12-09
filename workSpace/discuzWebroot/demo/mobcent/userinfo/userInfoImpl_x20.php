<?php
require_once './abstractUserInfo.php';
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../../source/function/function_forumlist.php';
require_once '../model/class_core.php';
require_once '../model/table/x20/mobcentDatabase.php';
require_once '../model/table/x20/table_forum_thread.php';
require_once '../model/table/x20/topic.php';
require_once '../tool/tool.php';
require_once '../tool/Thumbnail.php';
require_once '../Config/public.php';
require_once '../install/checkModule.php';
define('IN_MOBCENT',1);
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();

require_once libfile ( 'function/discuzcode' );

require_once '../model/table/x20/table_common_member.php';
require_once '../model/table/x20/mobcentDatabase.php';
class userInfoImpl_x20 extends abstractUserInfo {
	function getUserInfoObj() {
		$info = new mobcentGetInfo();
		$ip = get_client_ip();
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
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$fuid = $arrAccess['user_id'];
		$info = new mobcentGetInfo();
		$UserInfo = $info->getUserInfo(intval($fuid));
 
		if(empty($fuid))
		{   
			return C::t('common_member') -> userAccessError();
			exit();
		}
		else
		{
			$uid =$_G ['uid']= empty($_GET ['userId'])?$arrAccess['user_id']:$arrAccess['user_id'] == $_GET ['userId']? intval($arrAccess['user_id']):intval ( $_REQUEST ['userId']);  
			if(empty($uid))
			{
				return C::t('common_member') -> userAccessError();
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
			$followes = DB::fetch_first('SELECT COUNT(*) AS num FROM '.DB::table('home_friend').' WHERE fuid = '.$uid.' AND uid = '.$fuid);
			if ($followes['num']) {
				$followe = 1;
			}
			$blackfriend = DB::fetch_first ( 'SELECT COUNT(*) AS count FROM '.DB::table('home_blacklist').' WHERE buid='.$uid.' and uid='.$fuid.' limit 1' );
			if ($blackfriend ['count']) {
				$is_black = 1;
			}
 			$g_num = DB::fetch_first ( "SELECT count(*) as count FROM ".DB::table('home_friend')." WHERE fuid=".$uid);
			$f_num = DB::fetch_first ( "SELECT count(*) as count FROM ".DB::table('home_friend')." WHERE uid=".$uid );
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
			$status = DB::fetch_first ( "SELECT status FROM ".DB::table('common_member') ." WHERE uid=".$uid);
			$space = getuserbyuid ( $uid, 1 ); 
			if ($space ['uid'] == '') {
			echo '{"rs":0,"errcode":"01000000"}';
				exit ();
			}
			$space_pro=DB::fetch(DB::query("SELECT * FROM ".DB::table('common_member_profile')." where uid = ".$uid));
			$space = array_merge ( $space, $space_pro);  
			
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
					$query = DB::query ( "SELECT B.tid,B.attachment,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $thread['tid']);
					$pic = DB::fetch($query);
					if(!empty($pic)){
						if($ftp_isopen==0){
							$data ['pic_path'] = $topicInstance->parseTradeTopicImg($pic);
						}else{
							$data ['pic_path'] = $topicInstance->parseTradeTopicImgFTP($pic);
						}
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
					$query = DB::query ( "SELECT B.tid,B.attachment,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $thread['tid']);
					$pic = DB::fetch($query);
					if(!empty($pic)){
						if($ftp_isopen==0){
							$data ['pic_path'] = $topicInstance->parseTradeTopicImg($pic);
						}else{
							$data ['pic_path'] = $topicInstance->parseTradeTopicImgFTP($pic);
						}
					}else{
						$data ['pic_path'] = "";
					}
					$itemData[]=$data;
				}
				$user_data ['flag'] = 0;
			}
				
			/*end rx*/
			$topic_num=DB::fetch(DB::query("SELECT count(*) as nums FROM ".DB::table('forum_thread')." where authorid = ".$uid));
			$reply_posts_num=DB::fetch(DB::query("SELECT count(*) as nums FROM ".DB::table('forum_post')." where first!=1 and authorid = ".$uid));
			$list_count=DB::fetch(DB::query("SELECT count(*) as nums FROM ".DB::table('home_album')." where uid = ".$uid));
			$user_data ['is_black'] = ( int ) $is_black;  
			$user_data ['is_follow'] = (int)$followe;  
			$user_data ['icon_url'] = DISCUZSERVERURL;
			$user_data ['icon'] = userIconImg ( $uid ); 
			$user_data ['level_url'] = '';
			$user_data ['name'] = $space ['username'];
			$user_data ['email'] = $space ['email'];
			$user_data ['status'] = ( int )$online;
			$user_data ['gender'] = ( int ) $space ['gender'];
			$user_data ['email'] = $space ['email'];
			$user_data ['credits'] = $space ['credits'];  
			$user_data ['score'] = ( int )$space ['credits'];
			$user_data ['gold_num'] = ( int ) $space ['extcredits2'];  
			$user_data ['topic_num'] = ( int )$topic_num[nums];
			$user_data ['photoNum'] = ( int )$list_count[nums];
			$user_data ['reply_posts_num'] = ( int )$reply_posts_num[nums];
			$user_data ['essence_num'] = ( int ) $space ['digestposts'];
			$user_data ['follow_num'] = ( int )$g_num ['count']; 
			$user_data ['friend_num'] = ( int )$f_num ['count'];
			$user_data ['rs'] = 1;
			$user_data ['rs'] = 1;
			$user_data ['relational_num'] = 4;
			$space['groupid'];
			$groups = DB::fetch_first('select * from '.DB::table('common_usergroup').'  where groupid='.$space['groupid'].' limit 1');
			$user_data ['level'] = (int)$groups['stars']; 
			$user_data ['info'] = empty($itemData)?array():$itemData;
		}else{
			$user_data['rs'] = 0;
			$user_data['errcode'] = "010000001";
		}
		return $user_data;
	}

}

?>