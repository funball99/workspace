<?php
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../tool/constants.php';
require_once '../tool/Thumbnail.php';
require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'function/post' );
require_once '../public/common_json.php';
require_once ('./abstractUserReplyList.php');
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/mobcentDatabase.php';
class userReplyListImpl_x20 extends abstractUserReplyList {
	function getUserReplyList() {
		try{
			$topicInstance = new topic();
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
			$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
			$fuid = $arrAccess['user_id'];
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
			$page = $_GET ['page'] ? $_GET ['page'] : 1;  
			$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 10; 
			$start = $page ==1? 0 :($page - 1) * $limit;
			$posts = C::t ( 'forum_post' )->fetch_all_by_authorid ( 0, $uid, true, 'DESC', $start, $limit, 0, $invisible, $vfid );
			$count = C::t ( 'forum_post' )->count_by_authorid ($uid );
			require_once libfile ( 'function/attachment' );
			$smile_query = DB::query("SELECT code FROM ".DB::table('common_smiley')." WHERE type = 'smiley'");
			while ($smile_list = DB::fetch($smile_query)) {
				$smile_arr[] = $smile_list;
			}
			foreach($smile_arr as $sr){
				$smiles[]=$sr[code];
			}
			foreach ( $posts as $k => $val ) {
				if ($val ["attachment"] == 2 && $val["special"] != 2 ) {
					$pic = C::t('forum_thread')->fetch_all_threadimage($val);
					$pic_path = $topicInstance ->parseTradeTopicImg($pic);
						
				}
				
			if($val["special"] == 2) {
					$query = DB::query("SELECT * FROM ".DB::table('forum_trade')." WHERE tid='".$val['tid']."'  ORDER BY displayorder");
					while($trade = DB::fetch($query)) {
						$tradesaids[] = $trade['aid'];
						$tradespids[] = $trade['pid'];
					}
					$specialadd2 = 1;
					$tradespids = dimplode($tradespids);
					if($tradespids) {
						$query = DB::query("SELECT * FROM ".DB::table(getattachtablebytid($val['tid']))." WHERE pid IN ($tradespids)");
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
						$pic_path = $topicInstance ->parseTradeTopicImg($trades);
					}
				}
				
				$val_subject = DB::fetch(DB::query ( "SELECT * FROM ".DB::table('forum_thread')." WHERE tid= ".$val['tid'].' limit 0,1'));
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
				
				$post ['type'] = 0;  
				if ($pic_path){
					$post ['pic_path'] = $pic_path;  
				}else{
					$post ['pic_path'] = '';
				}
				$post ['topic_id'] = ( int ) $val['tid'];  
				$post ['title'] = sub_str($val_subject ['subject'],0,40);
				$post ['subject'] = $val_subject ['message'];
				$post ['last_reply_date'] = $val_subject ['lastpost'] . '000';
				$post ['user_id'] = $val ['authorid'];  
				$post ['user_nick_name'] = $val_subject ['author'];  
				$post ['hits'] = ( int ) $val_subject ['views'];  
				$post ['replies'] = ( int ) $val_subject ['replies']; 
				$post ['boardId'] = ( int ) $val_subject ['fid'];
				$boardName = DB::fetch(DB::query("SELECT name FROM ".DB::table('forum_forum')." WHERE fid=".$val_subject['fid']));
				$post ['board_name'] = $boardName[name];
				$post ['status'] = ( int ) $val_subject ['status'];
				$data_post [] = $post;
				unset ( $pic_path );
				
			}
 			//json::on_userReplyList($Config,$count,$page,$data_post);
			$thread_info = array (
					"img_url" => $Config ['pic_path'],
					"total_num" => (Int)$count,
					"page" => (Int)$page,
					"has_next" => ($page >= $N || $N == 1) ? 0 : 1,
					'list' => $data_post,
					"rs" => 1
			);
			return $thread_info;
		}catch(Exception $e)
		{
			$data_notice ['rs'] = 0;
			$data_notice ['error'] = '9999';
			return $data_notice;
		}
		
		}

}

?>