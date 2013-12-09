<?php
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once '../tool/constants.php';
require_once '../tool/Thumbnail.php';
require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'function/post' );
require_once ('./abstractUserReplyList.php');
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/class_core.php';
require_once '../model/table/x25/table_forum_post.php';
require_once '../model/table/x25/topic.php';
require_once '../public/mobcentDatabase.php';
require_once '../model/table/x25/table_forum_typeoptionvar.php';
require_once '../model/table/x25/table_forum_threadtype.php';
require_once '../model/table/x25/table_forum_threadclass.php';

class userReplyListImpl_x25 extends abstractUserReplyList {
	function getUserReplyList() {
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
		
		$uid = $arrAccess['user_id'] == $_REQUEST ['userId']? intval($arrAccess['user_id']):intval ( $_REQUEST ['userId']); 
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		$page = $_GET ['page'] ? $_GET ['page'] : 1;  
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 10;  
		$start = $page ==1? 0 :($page - 1) * $limit;
		$posts = M::t ( 'forum_post' )->fetch_all_by_authorid ( 0, $uid, true, 'DESC', $start, $limit, 0, $invisible, $vfid );
		$parameterCount = array (
				'forum_post',
				'forum_thread',
				$uid,
		);
		$count = DB::fetch_first ("SELECT COUNT(DISTINCT a.tid) as num FROM %t  a INNER JOIN %t  b ON a.tid = b.tid WHERE a.authorid=%d AND a.first=0 ORDER BY a.dateline DESC LIMIT ".$start.','.$limit,$parameterCount);
		require_once libfile ( 'function/attachment' );
		
		$smile_query = DB::query("SELECT code FROM ".DB::table('common_smiley')." WHERE type = 'smiley'");
		while ($smile_list = DB::fetch($smile_query)) {
			$smile_arr[] = $smile_list;
		}
		foreach($smile_arr as $sr){
			$smiles[]=$sr[code];
		}
		
		foreach($posts as $pid_key => $pid)
		{
			$tids[$pid['tid']][] = $pid_key;
			if ($pid ["attachment"] == 2) {
				$parameter = array (
						'forum_threadimage',
						( int ) $pid['tid']
				);
				$pic = DB::fetch_first ( "SELECT tid,attachment from %t where tid=%d", $parameter );
				$pic_path = $topicInstance->parseTargeImage($pic);
			}
			if($pid["special"] == 2) {
				$query = C::t('forum_trade')->fetch_all_thread_goods($pid["tid"]);
				foreach($query as $trade) {
					$tradesaids[] = $trade['aid'];
					$tradespids[] = $trade['pid'];
				}
				$specialadd2 = 1;
				if($tradespids) {
					foreach(C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$pid['tid'], 'pid', $tradespids) as $attach) {
						if($attach['isimage'] && is_array($tradesaids) && in_array($attach['aid'], $tradesaids)) {
							$trades[$attach['pid']]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
							$trades[$attach['pid']]['thumb'] = $attach['thumb'] ? getimgthumbname($trades[$attach['pid']]['attachurl']) : $trades[$attach['pid']]['attachurl'];
							$trades[$attach['pid']]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
							$filename = str_replace('forum/', '', $trades[$attach['pid']]['thumb']);
								
							$pic_path = $topicInstance->parseTradeTopicImg($filename);
						}
					}
				}
			}
			$parameter2 = array (
					C::t ( 'forum_thread' )->get_table_name (),
					$val = $pid['tid']
			);
			$val_subject = DB::fetch_first ( "SELECT * FROM %t WHERE tid=%d " . DB::limit ( 0, 1 ), $parameter2 );
			$message_query=DB::fetch(DB::query("SELECT message FROM ".DB::table('forum_post')." WHERE first=1 AND tid=".(Int)$val_subject['tid']));
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
			$val_subject[message]=$data_subject;
			// print_r($val_subject); 
			
			$post ['type'] = 0; 
			$post ['boardId'] = ( int ) $val_subject ['fid'];
			$boardName = DB::fetch(DB::query("SELECT name FROM ".DB::table('forum_forum')." WHERE fid=".$val_subject['fid']));
			$post ['board_name'] = $boardName[name];
			$post ['topic_id'] = ( int ) $val;  
			$post ['type_id'] = ( int ) $val_subject ['typeid'];  
			$post ['sort_id'] = ( int ) $val_subject ['sortid'];  
			$post ['title'] = sub_str($val_subject ['subject'],0,40);
			$post ['subject'] = $val_subject ['message'];
			$post ['last_reply_date'] = $val_subject ['lastpost'] . '000';
			$post ['user_id'] = $val_subject ['authorid'];  
			$post ['user_nick_name'] = $val_subject ['author'];  
			$post ['hits'] = ( int ) $val_subject ['views']; 
			$post ['replies'] = ( int ) $val_subject ['replies'];
			$post ['status'] = ( int ) $val_subject ['status'];
			if ($pic_path){
				$post ['pic_path'] = $pic_path;
			}else {
				$post['pic_path'] = '';
			}
			$data_post [] = $post;
			unset ( $pic_path );
		}
		/*[fen lei ming cheng]*/
		for($i=0;$i<count($data_post);$i++){
			$fenlei_name= C::t ( 'forum_threadtype' )->fetch_name_by_typeid ($data_post[$i][sort_id]);
			foreach($fenlei_name as $fl){
				$fenleimingcheng=$fl['name'];
				$data_post[$i][title]="[".$fenleimingcheng."]".$data_post[$i][title];
			}
		}
		for($i=0;$i<count($data_post);$i++){
			$fenlei_type= C::t ( 'forum_threadclass' )->fetch_all_by_typeid ($data_post[$i][type_id]);
			foreach($fenlei_type as $ftype){
				$fenleitypemingcheng=$ftype['name'];
				$data_post[$i][title]="[".$fenleitypemingcheng."]".$data_post[$i][title];
			}
		}
		/*[end fen lei ming cheng]*/
		$N = ceil ( ($count['num']-1) / $limit );
		$thread_info = array (
				"img_url" => $Config ['pic_path'],
				"total_num" => (Int)$count['num'],
				"page" => (Int)$page,
				"has_next" => ($page >= $N || $N == 1) ? 0 : 1,
				'list' => $data_post,
				"rs" => 1
		);
		return $thread_info;
		
		}
		

}

?>