<?php
define ( 'IN_MOBCENT', 1 );
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../config/config_ucenter.php';
require_once '../../source/function/function_forumlist.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/img_do.php';
require_once '../tool/Thumbnail.php';
require_once '../../source/function/function_forum.php';
require_once '../public/yz.php';
require_once '../tool/constants.php';
require_once ('./abstractSearchKeyword.php');
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/table_common_member.php';
require_once '../model/table/x20/table_forum_announcement.php';
require_once '../model/table/x20/mobcentDatabase.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();

class SearchKeywordImpl_x20 extends abstractSearchKeyword { 
	function getTopicList() {
		$setting_query = DB::query("SELECT svalue FROM ".DB::table('common_setting')." where skey='search'");
		while($setting_list = DB::fetch($setting_query)) {
			$setting_arr[] = $setting_list;
		}
		$settings[]=unserialize($setting_arr[0][svalue]);
		
		$searchctrl=$settings[0][forum][searchctrl]; /*sou suo shi jian xian zhi (second)*/
		$maxspm=$settings[0][forum][maxspm]==0?6000:$settings[0][forum][maxspm];/*60 miao zui da sou suo ci shu*/
		$maxsearchresults=$settings[0][forum][maxsearchresults];/*zui da sou suo jie guo*/
		if(empty($settings[0][forum][status])){
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000081';
			return $data_post;
			exit();
		}
		
		require_once '../model/table/x20/mobcentDatabase.php';
		$info = new mobcentGetInfo ();
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
		$group = $info->search_check_allow($accessSecret,$accessToken,$qquser);
		 
		$checksearch=(Int)$group['allowsearch'];
		$checkarr=array(2,3,6,10,18,66,7,11,19,67,14,22,70,26,74,82,15,23,71,27,75,83,30,78,90,31,79,94,95,87,91);
		if($group['groupid']!=1 && !in_array($checksearch, $checkarr))
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '01110001';
			return $data_post;
			exit();
		}
		
		if($group['groupid']!=1){
			/*sou suo ci shu xian zhi*/
			$file_time='count/time.txt';
			if(!file_exists($file_time)){
				fopen("$file_time", "w+");
				file_put_contents($file_time, time());
			}
			$last_time = file_get_contents($file_time);
			
			$file_cishu='count/cishu.txt';
			if(!file_exists($file_cishu)){
				fopen("$file_cishu", "w+");
				file_put_contents($file_cishu, '0');
			}
			if(time()-$last_time>60){
				file_put_contents($file_time,time());
				file_put_contents($file_cishu, '0');
			}
			
			$cn = file_get_contents($file_cishu);
			$cn++;
			file_put_contents($file_cishu, $cn);
			if($cn > $maxspm){
				$data_post['rs'] = 0;
				$data_post['searchctrl'] = (Int)$maxspm;
				$data_post['errcode'] = '04000083';
				return $data_post;
				exit();
			}else{
				if($searchctrl!=0){
					/*sou suo shi jian xian zhi*/
					$file_shijian='count/shijian.txt';
					if(!file_exists($file_shijian)){
						fopen("$file_shijian", "w+");
						file_put_contents($file_shijian, time());
					}
					$last_shijian = file_get_contents($file_shijian);
					if(time()-$last_shijian>$searchctrl){
						file_put_contents($file_shijian,time());
					}else{
						$data_post['rs'] = 0;
						$data_post['searchctrl'] = (Int)$searchctrl;
						$data_post['errcode'] = '04000082';
						return $data_post;
						exit();
					}
				}
			}
		}
		
		setglobal('groupid', $group['groupid']);
		global $_G;
		$_G ['fid'] = intval ( $_GET ['boardId'] );
		$checkObj = new check ();
		$forceindex = '';
		$picNew = new topic();
			
		$keys=$_GET['keyword']?echo_urldecode($_GET['keyword']):'';
		$boardId=$_GET['boardId']?(Int)$_GET['boardId']:0;
		
		$boardtool=$boardId==0?"":"AND fid=$boardId ";
		$threadlist_count = DB::fetch(DB::query("SELECT count(*) as num FROM ".DB::table('forum_thread')." where subject like '%".$keys."%' ".$boardtool));
		$data_count=(Int)$threadlist_count['num'];
		$maxsearchresults = $maxsearchresults==0?$data_count:$maxsearchresults;
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20;
		$start_limit = ($page - 1) * $limit;
		
		if($maxsearchresults <= $limit){
			$limit=$maxsearchresults;
			if($page > 1){
				$limit=0;
			}
		}elseif($maxsearchresults > $limit){
			if($page > 1){
				$lastpage=ceil($maxsearchresults/$limit); 
				if($page ==$lastpage ){
					$limit=$maxsearchresults % $limit;
				}
			}
		}
				
				
		/*
		if($page ==1)
		{
			$start_limit = 0;
		}
		else
		{
			$start_limit = ($page - 1) * $limit; 
		}
		$order = ($_GET ['sortby'] == 'publish' ? 'tid' : 'displayorder DESC,lastpost'); 
		*/                                                                        
		
		$topicInstance = new topic();
		$result_count_query = DB::query("SELECT tid from ".DB::table('forum_thread')." where subject like '%".$keys."%' ".$boardtool."limit $start_limit,$limit");
		while($result_count_list = DB::fetch($result_count_query)) {
			$result_count[] = $result_count_list;
		}
		$threadlist_query = DB::query("SELECT * FROM ".DB::table('forum_thread')." where subject like '%".$keys."%' ".$boardtool." order by lastpost desc limit $start_limit,$limit");
		while($threadlist_list = DB::fetch($threadlist_query)) {
			$threadlist[] = $threadlist_list;
		}
		
		require_once libfile ( 'function/attachment' );
		
		foreach($threadlist as $key=>$thread){
			$message_query=DB::fetch(DB::query("SELECT message FROM ".DB::table('forum_post')." WHERE first=1 AND tid=".(Int)$thread['tid']));
			preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i",  $message_query['message'] ,$matches);
			$patten  = array("\r\n", "\n", "\r");
			$data_subject = str_replace($matches[1], '', $message_query ['message']);
			$data_subject =str_replace($patten, '', $data_subject);
			$data_subject = preg_replace("#(\w*)\[.*?\](\w*)#","$1$2",$data_subject);
			$data_subject =trim($data_subject);
			$data_subject = sub_str($data_subject, 0,40);
			$threadlist[$key][message]=$data_subject;
		}
		
		foreach ( $threadlist as $k => $group ) {
			$query = DB::query("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$group['tid']." AND p.first =1");
			while ($rows = DB::fetch($query)) {
				$ForumImg = $rows;
			}
			if ($ForumImg ["attachment"] == 2) {
				$pic = C::t('forum_thread') ->fetch_all_threadimage($group);
				if(!empty($pic)){
					$filename = $picNew->parseTradeTopicImg($pic);
				}
			}
			if($group ["special"] ==2)
			{
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
					$filename = $topicInstance ->parseTradeTopicImg($trades);
				}
			}
			$data ['board_id'] = ( int ) $group ['fid']; 
			$data ['topic_id'] = ( int ) $group ['tid']; 
			$data ['type_id'] = ( int ) $group ['typeid'];
			$data ['sort_id'] = ( int ) $group ['sortid'];
			if($group ["special"]==1){
				$data ['vote'] = (int)1;
			}else{
				$data ['vote'] = (int)0;
			}
			$data ['title'] = sub_str($group ['subject'], 0,40);
			$data ['subject'] = $group ['message'];
			$data ['user_id'] = ( int ) $group ['authorid']; 
			$data ['last_reply_date'] = ($group ['lastpost']) . "000"; 
			if(empty($group ['author']))
			{
				$data ['user_nick_name'] =Common::get_unicode_charset('\u533f\u540d');;
			}
			else
			{
				$data ['user_nick_name'] = $group ['author']; 
			}
			$data ['hits'] = ( int ) $group ['views']; 
			$data ['replies'] = ( int ) $group ['replies']; 
			$data ['status'] = ( int ) $group ['status']; 
			$data ['essence'] = ( int ) $group ['digest'] >0|| ( int ) $group ['icon'] ==9 || (int ) $group ['stamp'] ==0? 1 : 0; 
			$data ['top'] = ( int ) $group ['displayorder'] >0|| ( int ) $group ['icon'] ==13 || (int ) $group ['stamp'] ==4? 1 : 0;
			                                                      
			                                                      
			$data ['hot'] = ( int ) $group ['stamp'] ==1|| ( int ) $group ['icon'] ==10? 1 : 0;
		    if ($filename) {
						$data ['pic_path'] = $filename;
					}else {
						$data ['pic_path'] = '';
					}
			unset ( $filename );
			$ret_pic_path = '';
			$data_thread [] = $data;
		} 
		if(empty($_GET ['digest'])&& empty($_GET ['displayorder']) && empty($_GET ['sortby']) && $page ==1)
		{
			$announcement = C::t ( 'forum_announcement' )->fetch_all_by_displayorder ();
			while ($announ = DB::fetch($announcement)) {
				$data_announ [] = array (
						"announce_id" => $announ ["id"],
						"author" => $announ ["author"],
						"board_id" => '',
						"forum_id" => '',
						"start_date" => $announ ["starttime"] . '000',
						"title" => preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$announ ["subject"])
				);
			}
		}
		
		
		$thread_info = array (
				"img_url" => DISCUZSERVERURL,
				"total_num" =>  (Int)$data_count,
				"page" => (Int)$page,
				/*"anno_list" => $data_announ,*/
				'list' => empty($data_thread)?array():$data_thread,
				'rs' => 1 
		);
		if ($fid == '') {
			unset ( $thread_info ['anno_list'] );
		}
		
		return $thread_info;
	}

}

?>