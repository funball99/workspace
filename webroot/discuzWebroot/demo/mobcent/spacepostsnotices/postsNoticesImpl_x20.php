<?php
require_once './abstractpostsNotices.php';
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../tool/Thumbnail.php';
require_once '../public/common_json.php';
require_once '../Config/public.php';
require_once '../model/table/x20/table_common_member.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once libfile ( 'function/forumlist' );
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/mobcentDatabase.php';
class postsNoticesImpl_x20 extends abstractpostsNotices {
	public function getpostsNoticesObj() {
		$_GET ['mod'] = 'space';
		$_GET ['do'] = 'notice';
		$topicInstance = new topic();
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $_G ['uid'] = $arrAccess['user_id'];
		$info = new mobcentGetInfo();
		if(empty($uid))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
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
		$isread = $_GET ['isread'] ? $_GET ['isread'] : 1;
		$type = $_GET ['type'] = $_GET ['type'] ? $_GET ['type'] : 'at';
		$perpage = 30;
		$perpage = mob_perpage ( $perpage );
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20;
		$start = ($page <= 1) ? 0 : (($page - 1) * $limit);
		if ($page < 1) $page = 1;
		
	//echo $start_limit.'@'.($limit+$start_limit).'<br>';
		$list = array ();
		$mynotice = $count = 0;
		$multi = '';
		$topicInstance = new topic();
		if ($type == 'at') {  
			$post_query=DB::query("SELECT * FROM ".DB::table('forum_post')." where first != 1 order by dateline desc");
			while($post_list=DB::fetch($post_query)){
				$post_arrs[]=$post_list;
			} 
			foreach($post_arrs as $pt_key=>$pt_val){ 
				if(strstr($pt_val[message],$username)!=""){
					$var_arr[]=$pt_val; 
				}
			}
			for($i = $start_limit;$i < ($limit+$start_limit);$i++){
				$post_arr[]=$var_arr[$i];
			}
			$count=count($var_arr);
			$flag=empty($post_arr[0]) ? false:true;
			//print_r($post_arr);exit;
		}else{ 
			$tid_query=DB::query("SELECT tid FROM ".DB::table('forum_thread')." where authorid=".$uid);
			while($tid_list=DB::fetch($tid_query)){
				$tid_arr[]=$tid_list;
			}
			if(empty($tid_arr)){
				$count=0;
				$flag=false;
			}else{
				foreach($tid_arr as $ta){
					$tids[]=$ta[tid];
				}
				$mytid=implode(',', $tids);
				$post_query=DB::query("SELECT * FROM ".DB::table('forum_post')." where first != 1 and tid in(".$mytid.") order by dateline desc limit $start_limit,$limit");
				while($post_list=DB::fetch($post_query)){
					$var_arr[]=$post_list;
				}
				$postCount=DB::fetch(DB::query("SELECT count(*) as nums FROM ".DB::table('forum_post')." where first != 1 and tid in(".$mytid.")"));
				$count=$postCount[nums];
				$flag=empty($var_arr) ? false:true;
			}
		}
		
		
		//echo $flag;exit;
		//print_r(count($var_arr));exit;
		if($flag){
			foreach ( $var_arr as $k => $v ) {
				$post_message=str_replace("\r", "", $v ['message']);
				$post_message=str_replace("\n", "", $v ['message']); 
				/*topic_id*/
				$list_sub ['topic_id'] = (int)$v['tid'];
				/*borad_name*/
				$board_query=DB::fetch(DB::query("SELECT name FROM ".DB::table('forum_forum')." where fid=".$v[fid]));
				 
				
				$list_sub ['board_name'] = $board_query[name];
				/*subject_query*/
				$subject_query=DB::fetch(DB::query("SELECT subject FROM ".DB::table('forum_thread')." where tid=".$v[tid]));
				$list_sub ['topic_subject'] = $subject_query['subject'];
				
 
				//echo $post_message; 
				if(strstr($post_message,'[quote]')==""){
					/*topic_content*/
					$subject_query=DB::fetch(DB::query("SELECT message FROM ".DB::table('forum_post')." where first=1 and tid=".$v[tid]));
					$list_sub ['topic_content'] = $this->subjectstr($subject_query['message']);
					/*topic_url*/
					preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i", $subject_query['message'],$mat);
					$list_sub ['topic_url'] = $this->getImg($mat[1][0]);
					
					/*reply_content*/
					$list_sub ['reply_content'] = $this->subjectstr($post_message);
					/*reply_url*/
					preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i", $post_message,$mas); 
					$list_sub ['reply_url'] = $this->getImg($mas[1][0]);
				}else{
					preg_match_all ('/\[quote\](.+)\[\/quote\](.+)/', $post_message ,$postmatches);
					/*topic_content*/
					$list_sub ['topic_content'] = $this->subjectstr($postmatches[1][0]);
					/*topic_url*/
					preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i", $postmatches[1][0],$mat); 
					$list_sub ['topic_url'] = $this->getImg($mat[1][0]);
					/*reply_content*/
					$list_sub ['reply_content'] = $this->subjectstr($postmatches[2][0]);
					/*reply_url*/
					preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i", $postmatches[2][0],$mas);
					$list_sub ['reply_url'] = $this->getImg($mas[1][0]);
				}
				 
				$list_sub ['reply_remind_id'] = ( int ) $v ['id'];
				$list_sub ['reply_nick_name'] = $v ['author'];
				$list_sub ['user_id'] = ( int ) $v ['authorid'];
				$list_sub ['is_read'] = ( int ) 1 - $v ['new'];
				$list_sub ['replied_date'] = $v ['dateline'] . '000';
				$list_sub ['create_time'] = $v ['dateline'] . '000';
				$list_sub ['icon'] = userIconImg ( $v ['authorid'] );
				$data_list [] = $list_sub;
			} 
		} 
		if($count-($page * $limit) > 0){
			$has_next = (int)1;
		}else{
			$has_next = (int)0;
		}
		
		$data_notice ['rs'] = 1;
		$data_notice ['has_next'] = $has_next; 
		$data_notice ['page'] = $page;
		$data_notice ['total_num'] = ( int ) ($count);
		$data_notice ['icon_url'] = DISCUZSERVERURL;
		$data_notice ['list'] = $flag? $data_list:array();
		return $data_notice;
	}
	
	function subjectstr($str){
		preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i", $str,$matches);
		$patten  = array("\r\n", "\n", "\r");
		$data_subject = str_replace($matches[1], '',$str);
		$data_subject = str_replace($patten, '', $data_subject);
		$data_subject = preg_replace("#(\w*)\[.*?\](\w*)#","$1$2",$data_subject);
		$data_subject = trim($data_subject);
		$strRes = sub_str($data_subject, 0,140);
		return $strRes;
	}
	function getPic($id){
		$topicInstance = new topic();
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
		$query = DB::query ( "SELECT B.tid,B.attachment,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".$id);
		$pic = DB::fetch($query); 
		if(!empty($pic)){
			if($ftp_isopen==0){
				$picUrl = $topicInstance->parseTradeTopicImg($pic);
			}else{
				$picUrl = $topicInstance->parseTradeTopicImgFTP($pic);
			}
		}else{
			$picUrl = "";
		}
		return $picUrl;
	}
	
	function getImg($aid){ 
		$topicInstance = new topic();
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
		if(!empty($aid)){
			$aid_query=DB::fetch(DB::query("SELECT tableid FROM ".DB::table('forum_attachment')." where aid=".$aid));
			$pic=DB::fetch(DB::query("SELECT tid,attachment,dateline FROM ".DB::table('forum_attachment_').$aid_query[tableid]." where aid=".$aid));
		
			if($ftp_isopen==0){
				$picUrl = $topicInstance->parseTradeTopicImg($pic);
			}else{
				$picUrl = $topicInstance->parseTradeTopicImgFTP($pic);
			}
		}else{
			$picUrl = "";
		}
		return $picUrl;
	}
}

?>