<?php
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../public/yz.php';
require_once '../tool/Thumbnail.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile ( 'function/forumlist' );
require_once '../tool/constants.php';
require_once '../Config/public.php';
require_once '../public/mobcentDatabase.php';
require_once ('./abstractHomeTopicList.php');
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_common_member.php';
require_once '../model/table/x25/table_forum_typeoptionvar.php';
require_once '../model/table/x25/table_forum_threadtype.php';
require_once '../model/table/x25/table_forum_threadclass.php';

class homeTopicListImpl_x25 extends abstractHomeTopicList {
	function getHomeTopicList() {
		$info = new mobcentGetInfo ();
		$_G ['fid'] = intval ( $_GET ['boardId'] );
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
			$space = $info->getUserInfo ( intval ($uid) );
			$_G = array_merge ( $_G, $space );
			global $_G;
			$_G['groupid'] =$group['groupid'];
			$forum = $info->getForumSub ( $_G ['fid'] );
			$_G ['forum'] = array_merge ( $_G ['forum'], $forum );
			$checkObj = new check ();
			$resulst = $checkObj->viewperm ();
			if ($resulst ['error']) {
				echo $resulst ['message'];
				exit ();
			}
		$fid = $_GET ['boardId'];
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20;
		$start_limit = ($page - 1) * $limit;
		$order = ($_GET ['sortby'] == 'publish' ? 'tid' : 'displayorder,lastpost');
		$digest = $_GET ['digest'] ? array (
				3,
				2,
				1
		) : array (
				3,
				2,
				1,
				0
		);
		$displayorder = $_GET ['displayorder'] ? array (
				3,
				2,
				1
		) : array (
				3,
				2,
				1,
				0
		); 
		$keywords = $_GET ['keyword'];
		switch ($_GET ['sortby']) {
			case 'essence' :
				$digest = array (
				3,
				2,
				1
				);
				$jinghua = 1;
				break;
			case 'hot' :
				$stamp = 1;
				break;
		}
		
		$sort = $_GET ['sort']; 
		switch ($sort) {
			case 1 :
				$sort = 'asc';
				break;
			case 0 :
			case '' :
				$sort = 'desc';
				break;
		}
		$forceindex = '';
		
		$filterarr1 = array ();
		$filterarr1 ['inforum'] = $fid; 
		$filterarr1 ['sticky'] = 4; 
		$filterarr1 ['digest'] = $digest;
		$filterarr1 ['displayorder'] = $displayorder;
		$filterarr1 ['keywords'] = $keywords;
		$topicInstance = new topic();
		$tids =$info ->forum_display($fid,$topicInstance);
		if ($stamp && empty ( $fid )) {
			$parameter = array (
					'forum_thread',
			);
			
			$threadlist = DB::fetch_all ( "SELECT * FROM %t t WHERE  t.displayorder >'-1' AND (t.icon =10 or stamp =1) AND t.fid in(".$tids.") ORDER BY t.tid desc" . DB::limit ( $start_limit, $limit ), $parameter, 'tid' );
		} elseif ($stamp && ! empty ( $fid )) {
			$parameter = array (
					'forum_thread',
					'forum_forum',
					$stamp,
					$fid
			);
			$threadlist = DB::fetch_all ( "SELECT * FROM %t t,%t,f WHERE t.stamp=%s and t.fid=%d AND t.fid =f.fid AND displayorder >'-1' " . DB::limit ( $start_limit, $limit ), $parameter, 'tid' );
		}elseif ($jinghua && empty ( $fid )) {  
			$threadlist_query = DB::query("SELECT * FROM ".DB::table('forum_thread')." WHERE (stamp=0 or icon=9 or digest in(1,2,3)) ORDER BY lastpost desc limit $start_limit,$limit");
			while ($threadlist_list = DB::fetch($threadlist_query)) {
				$threadlist[] = $threadlist_list;
			}
			$threadlist_num = DB::fetch(DB::query("SELECT count(*) as num FROM ".DB::table('forum_thread')." WHERE (stamp=0 or icon=9 or digest in(1,2,3))"));
			$threadlist_count=$threadlist_num[num];
		} elseif(empty ( $fid )) {
			$digest = implode(',', $digest);
			$displayorder = implode(',', $displayorder);
			$parameter = array (
					'forum_thread',
			);
			$threadlist = DB::fetch_all ( "SELECT * FROM %t t WHERE   t.fid in(".$tids.") AND t.displayorder >'-1'  and t.displayorder in(".$displayorder.") and t.digest in(".$digest.") ORDER BY t.dateline DESC  " . DB::limit ( $start_limit, $limit ), $parameter, 'tid' );
			$threadlist_num = DB::fetch_all ( "SELECT count(*) as num FROM %t t WHERE   t.fid in(".$tids.") AND t.displayorder >'-1'  and t.displayorder in(".$displayorder.") and t.digest in(".$digest.") " , $parameter);
			$threadlist_count=$threadlist_num[0][num];
		} else {
			$digest = implode(',', $digest);
			$displayorder = implode(',', $displayorder);
			$parameter = array (
					'forum_thread',
			);
			$threadlist = DB::fetch_all ( "SELECT * FROM %t t WHERE   t.fid =".$fid." AND t.displayorder >'-1'  and t.displayorder in(".$displayorder.") and t.digest in(".$digest.") ORDER BY t.dateline DESC  " . DB::limit ( $start_limit, $limit ), $parameter, 'tid' );
			$threadlist_num = DB::fetch_all ( "SELECT count(*) as num FROM %t t WHERE    t.fid =".$fid."  AND t.displayorder >'-1'  and t.displayorder in(".$displayorder.") and t.digest in(".$digest.") " , $parameter);
			$threadlist_count=$threadlist_num[0][num];
		}
		
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
			$parameterforum = array (
					'forum_forum',
					( int ) $group ['fid']
			);
			$status = DB::fetch_first ( "SELECT status from %t where fid=%d", $parameterforum );
			if($status['status'] == 0)
			{
				continue;
			}
			if ($group ["attachment"] == 2) {
				$parameter = array (
						'forum_threadimage',
						( int ) $group ['tid']
				);
				$pic = DB::fetch_first ( "SELECT tid,attachment from %t where tid=%d", $parameter );
				$pic_path = $topicInstance->parseTargeImage($pic);
			}
			if($group ["special"] ==2)
			{
				$query = C::t('forum_trade')->fetch_all_thread_goods($group['tid']);
				foreach($query as $trade) {
					$tradesaids[] = $trade['aid'];
					$tradespids[] = $trade['pid'];
				}
				
				$specialadd2 = 1;
				if($tradespids) {
					foreach(C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$group['tid'], 'pid', $tradespids) as $attach) {
						if($attach['isimage'] && is_array($tradesaids) && in_array($attach['aid'], $tradesaids)) {
							$trades[$attach['pid']]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
							$trades['thumb'] = $attach['thumb'] ? getimgthumbname($trades[$attach['pid']]['attachurl']) : $trades[$attach['pid']]['attachurl'];
							$trades[$attach['pid']]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
						}
					}
				}
			
				if(!empty($trades))
				{
					$trades['thumb'] =str_replace('forum/', '', $trades['thumb']);
					$pic_path = $topicInstance ->parseTargeImage($trades);
					
				}
			}
			$data ['board_id'] = ( int ) $group ['fid']; 
			$data ['topic_id'] = ( int ) $group ['tid']; 
            $data ['type_id'] = ( int ) $group ['typeid'];
            $data ['sort_id'] = ( int ) $group ['sortid'];
            $title = sub_str($group ['subject'], 0, 15);
			$data ['title'] = $title;
			$data ['subject'] = $group ['message'];
			$data ['user_id'] = ( int ) $group ['authorid'];
			$data ['last_reply_date'] = $group ['dateline'] . "000";
			$data ['user_nick_name'] = $group ['author'];
			$data ['hits'] = ( int ) $group ['views'];
			if($group ["special"]==1){
				$data ['vote'] = (int)1;
			}else{
				$data ['vote'] = (int)0;
			}
			$data ['replies'] = ( int ) $group ['replies'];
			$data ['top'] = ( int ) $group ['displayorder'] >0 || ( int ) $group ['icon'] ==13 || (int ) $group ['stamp'] ==4? 1 : 0;
			$data ['status'] = ( int ) $group ['status'];
			$data ['essence'] = ( int ) $group ['digest'] >0|| ( int ) $group ['icon'] ==9 || (int ) $group ['stamp'] ==0? 1 : 0; 
			$data ['hot'] = ( int ) $group ['stamp'] ==1|| ( int ) $group ['icon'] ==10? 1 : 0;
			if ($pic_path){
				$data ['pic_path'] = $pic_path;
			} else {
				$data['pic_path'] = '';
			}
		
			unset ( $pic_path );
			$data_thread [] = $data;
			 
		}
		//print_r($data_thread);exit;
		
		/*[fen lei ming cheng]*/
		$topicInstance = new topic();
		for($i=0;$i<count($data_thread);$i++){
			$fenlei_name= C::t ( 'forum_threadtype' )->fetch_name_by_typeid ($data_thread[$i][sort_id]);
			foreach($fenlei_name as $fl){
				$fenleimingcheng=$topicInstance->replaceHtmlAndJs($fl['name']);
				$data_thread[$i][title]="[".$fenleimingcheng."]".$data_thread[$i][title];
			}
		}
		for($i=0;$i<count($data_thread);$i++){
			$fenlei_type= C::t ( 'forum_threadclass' )->fetch_all_by_typeid ($data_thread[$i][type_id]);
			foreach($fenlei_type as $ftype){
				$fenleitypemingcheng=$topicInstance->replaceHtmlAndJs($ftype['name']);
				$data_thread[$i][title]="[".$fenleitypemingcheng."]".$data_thread[$i][title];
			}
		}
		/*[end fen lei ming cheng]*/
		
		$announcement = C::t ( 'forum_announcement' )->fetch_all_by_displayorder ();
		foreach ( $announcement as $k => $announ ) {
			$data_announ [] = array (
					"announce_id" => $announ ["id"],
					"author" => $announ ["author"],
					"board_id" => '',
					"forum_id" => '',
					"start_date" => $announ ["starttime"] . '000',
					"title" => $announ ["subject"]
			);
		}
		
		if ($stamp) {
			$tids =$info ->forum_display($fid,$topicInstance);
			$parameter = array (
					'forum_thread',
			);
			$num = DB::fetch_first ( "SELECT count(*) as num FROM %t t WHERE  t.displayorder >'-1'  AND (t.icon =10 or stamp =1) AND t.fid in(".$tids.")  ORDER BY t.tid desc", $parameter, 'tid' );
			$count = $num ['num'];
		} else {
			$count = $threadlist_count;
			
		}
		$N = ceil ( ($count-1) / $limit );
		$thread_info = array (
				'has_next' =>($page>=$N || $N==1) ?0:1,
				"img_url" => DISCUZSERVERURL,
				"total_num" => (Int)$count,
				"page" => (Int)$page,
				/*"anno_list" => $data_announ,*/
				'list' => $data_thread,
				'rs' => 1
		);
		if ($fid == '') {
			unset ( $thread_info ['anno_list'] );
		}
		return $thread_info ;
		exit ();
		
		}

}

?>