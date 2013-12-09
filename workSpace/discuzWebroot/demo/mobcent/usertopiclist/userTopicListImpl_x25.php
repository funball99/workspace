<?php
require_once './abstractUserTopicList.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once '../tool/constants.php';
require_once '../tool/Thumbnail.php';
require_once '../public/mobcentDatabase.php';
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_forum_typeoptionvar.php';
require_once '../model/table/x25/table_forum_threadtype.php';
require_once '../model/table/x25/table_forum_threadclass.php';

class userTopicListImpl_x25 extends abstractUserTopicList {
	function getUserTopicList() {
		$data_thread = array ();
		$info = new mobcentGetInfo();
		$topicInstance = new topic();
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
		$uid =$_G ['uid']= $arrAccess['user_id'] == $_REQUEST ['userId']? intval($arrAccess['user_id']):intval ( $_REQUEST ['userId']); 
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		$page = $_GET ['page'] ? $_GET ['page'] : 1;  
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 10;  
		$start = $page ==1? 0 :($page - 1) * $limit;
		$displayorder = intval ( $_GET ['displayorder'] ) ? $_GET ['displayorder'] : null; 
		$parameter = array('forum_thread', $uid);
		$threadlist =  DB::fetch_all("SELECT * FROM %t WHERE authorid = %d AND displayorder != -1 ORDER BY lastpost DESC ".DB::limit($start, $limit), $parameter);
		$count = C::t ( 'forum_thread' )->count_by_special ( '', $uid );
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
			if($group["special"] == 2) {
				$query = C::t('forum_trade')->fetch_all_thread_goods($group["tid"]);
				foreach($query as $trade) {
					$tradesaids[] = $trade['aid'];
					$tradespids[] = $trade['pid'];
				}
				$specialadd2 = 1;
				if($tradespids) {
					foreach(C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$group['tid'], 'pid', $tradespids) as $attach) {
						if($attach['isimage'] && is_array($tradesaids) && in_array($attach['aid'], $tradesaids)) {
							$trades[$attach['pid']]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
							$trades[$attach['pid']]['thumb'] = $attach['thumb'] ? getimgthumbname($trades[$attach['pid']]['attachurl']) : $trades[$attach['pid']]['attachurl'];
							$trades[$attach['pid']]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
							$filename = str_replace('forum/', '', $trades[$attach['pid']]['thumb']);
							$pic_path = $topicInstance->parseTargeImage($filename);
						}
					}
				}
			}
			
			/*if ($group ["attachment"] == 2) {
				$parameter = array (
						'forum_threadimage',
						( int ) $group ['tid']
				);
				$pic = DB::fetch_first ( "SELECT tid,attachment from %t where tid=%d", $parameter );
				if(!empty($pic['attachment']))
				{
					$pic_path = $topicInstance->parseTargeImage($pic);
				} 
			}*/
			
			$data ['type'] = 0;  
			$data ['board_id'] = ( int ) $group ['fid'];
			$boardName = DB::fetch(DB::query("SELECT name FROM ".DB::table('forum_forum')." WHERE fid=".$group ['fid']));
			$data ['board_name'] = $boardName[name];
			$data ['topic_id'] = ( int ) $group ['tid'];  
			$data ['type_id'] = ( int ) $group ['typeid'];
			$data ['sort_id'] = ( int ) $group ['sortid'];
			$data ['title'] = sub_str($group ['subject'],0,40);
			$data ['subject'] = $group ['message'];
			$data ['user_id'] = ( int ) $group ['authorid'];  
			$data ['last_reply_date'] = $group ['lastpost'] . '000';
			$data ['user_nick_name'] = $group ['author'];  
			$data ['hits'] = ( int ) $group ['views'];  
			$data ['replies'] = ( int ) $group ['replies'];  
			$data ['top'] = ( int ) $group ['first'];  
			$data ['status'] = ( int ) ! $group ['status']; 
			$data ['essence'] = ( int ) $group ['digest'] ? 1 : 0;  
			$data ['hot'] = ( int ) $group ['highlight'] ? 1 : 0;  
			$query = DB::query ( "SELECT B.*,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $group['tid']);
			$pic = DB::fetch($query); 
			if(!empty($pic)){
				$data ['pic_path'] = $topicInstance->parseTargeImage($pic);
			}else{
				$data ['pic_path'] = "";
			}
			$data_thread [] = $data;
			unset ( $pic_path );
		}
		
		/*[fen lei ming cheng]*/
		for($i=0;$i<count($data_thread);$i++){
			$fenlei_name= C::t ( 'forum_threadtype' )->fetch_name_by_typeid ($data_thread[$i][sort_id]);
			foreach($fenlei_name as $fl){
				$fenleimingcheng=$fl['name'];
				$data_thread[$i][title]="[".$fenleimingcheng."]".$data_thread[$i][title];
			}
		}
		for($i=0;$i<count($data_thread);$i++){
			$fenlei_type= C::t ( 'forum_threadclass' )->fetch_all_by_typeid ($data_thread[$i][type_id]);
			foreach($fenlei_type as $ftype){
				$fenleitypemingcheng=$ftype['name'];
				$data_thread[$i][title]="[".$fenleitypemingcheng."]".$data_thread[$i][title];
			}
		}
		/*[end fen lei ming cheng]*/
		
		
		$thread_info = array ();

		$N = ceil ( ($count-1) / $limit );
		$thread_info = array (
				"img_url" => $Config ['pic_path'],
				"total_num" => ( int ) $count,
				"has_next" => ($page >= $N || $N == 1) ? 0 : 1,
				"page" => (Int)$page,
				'list' => $data_thread,
				"rs" => 1
		);
		return $thread_info;
		}


}

?>