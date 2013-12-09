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
require_once '../public/common_json.php';
require_once ('./abstractUserTopicList.php');
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/mobcentDatabase.php';
class userTopicListImpl_x20 extends abstractUserTopicList {
	function getUserTopicList() {
		$topicInstance = new topic();
		$info = new mobcentGetInfo();
		$data_thread = array ();
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
		try{
			$start = $page ==1? 0 :($page - 1) * $limit;
			$displayorder = intval ( $_GET ['displayorder'] ) ? $_GET ['displayorder'] : null;  
			$query = C::t ( 'forum_thread' )->fetch_all_by_authorid_displayorder($uid,$start,$limit);
		
			$fids = $forums = array();
			while($value = DB::fetch($query)) {
				$threadlist[] = $value;
			}
			$count = C::t ( 'forum_thread' )->fetch_all_by_authorid_total($uid);
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
					$query = DB::query("SELECT * FROM ".DB::table('forum_trade')." WHERE tid='".$group['tid']."'  ORDER BY displayorder");
					while($trade = DB::fetch($query)) {
						$tradesaids[] = $trade['aid'];
						$tradespids[] = $trade['pid'];
					}
					$specialadd2 = 1;
					$tradespids = dimplode($tradespids);
					if($tradespids) {
						$query = DB::query("SELECT * FROM ".DB::table(getattachtablebytid($group['tid']))." WHERE pid IN ($tradespids)");
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
				
				if ($group ["attachment"] == 2 && $group["special"] != 2 ) {
					$pic = DB::fetch_first ( "SELECT tid,attachment from ".DB::table('forum_threadimage')." where tid=".( int ) $group ['tid']);
					$pic_path = $topicInstance ->parseTradeTopicImg($pic);
					
				}
				
				$data ['type'] = 0;  
				$data ['board_id'] = ( int ) $group ['fid'];  
				$boardName = DB::fetch(DB::query("SELECT name FROM ".DB::table('forum_forum')." WHERE fid=".$group ['fid']));
				$data ['board_name'] = $boardName[name];
				$data ['topic_id'] = ( int ) $group ['tid'];  
				$data ['title'] = sub_str($group ['subject'],0,40);
				$data ['subject'] = $group ['message'];
				$data ['user_id'] = ( int ) $group ['authorid']; 
				$data ['last_reply_date'] = $group ['dateline'] . '000';
				$data ['user_nick_name'] = $group ['author'];  
				$data ['hits'] = ( int ) $group ['views'];  
				$data ['replies'] = ( int ) $group ['replies']; 
				$data ['top'] = ( int ) $group ['first']; 
				$data ['status'] = ( int ) ! $group ['status'];  
				$data ['essence'] = ( int ) $group ['digest'] ? 1 : 0;  
				$data ['hot'] = ( int ) $group ['highlight'] ? 1 : 0;  
				if ($pic_path){
					$data ['pic_path'] = $pic_path;  
				}else{
					$data ['pic_path'] = '';
				}
				$data_thread [] = $data;
				unset ( $pic_path );
			}
			$thread_info = array ();
			$data_notice = array (
					"img_url" => $Config ['pic_path'],
					"total_num" => ( int ) $count,
					"page" => (Int)$page,
					'list' => $data_thread,
					"rs" => 1
			);
			return $data_notice;
		}catch(Exception $e)
		{
			$data_notice ['rs'] = 0;
			$data_notice ['error'] = '9999';
			return $data_notice;
		}
		}

}

?>