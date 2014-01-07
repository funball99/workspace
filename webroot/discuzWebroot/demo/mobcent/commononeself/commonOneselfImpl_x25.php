<?php
require_once './abstarctCommonOneself.php';
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../source/function/function_threadsort.php';
require_once '../model/table/x25/table_surround_user.php';
require_once '../tool/Thumbnail.php';
require_once '../../config/config_ucenter.php';
require_once '../Config/public.php';
require_once '../tool/img_do.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once '../public/mobcentDatabase.php';
require_once '../public/yz.php';
require_once '../model/table/x25/table_common_member_profile.php';
require_once '../model/table/x25/forum.php';
require_once '../model/table/x25/topic.php'; 
require_once libfile('function/forumlist');
require_once libfile('function/discuzcode');
require_once libfile('function/post');
require_once libfile ( 'function/attachment' );
require_once '../tool/tool.php';
require_once '../model/table/x25/table_forum_thread.php';

class commonOneselfImpl_x25 extends abstarctCommonOneself {
	public function getCommonOneselfObj() {
		$forumclass = new forum();
		$info = new mobcentGetInfo ();
		$_G ['fid'] = intval ( $_GET ['boardId'] );
		if ($_G ['fid']) {
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
			if(empty($accessSecret) || empty($accessToken))
			{
				$group = $info-> sel_QQuser($qquser);
			}
			else
			{
				$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
				$userId = $arrAccess['user_id'];
				if(empty($userId))
				{
					return $info -> userAccessError();
					exit();
				}
				$group = $info-> sel_group_by_uid($userId);
			}
			if(!$_G['forum']['viewperm'] && !$group['readaccess'])
			{
				$data_post['rs'] = 0;
				$data_post['errcode'] = '01110001';
				return $data_post;
				exit();
			}
			$space = $info->getUserInfo ( intval ( $userId ) );
			$_G = array_merge ( $_G, $space );
			$forum = $info->getForumSub ( $_G ['fid'] );
			$_G ['forum'] = array_merge ( $_G ['forum'], $forum );
			$checkObj = new check ();
			$resulst = $checkObj->viewperm ();
			if ($resulst ['error']) {
				echo $resulst ['message'];
				exit ();
			}
		}
		
		
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20; 
		$start_limit = $page==1?0:($page - 1) * $limit - 1; 
		$_G ['tid'] = $tid = $_GET ['topicId'] ; 
		$_G['forum_pagebydesc'] = $_GET ['sortby'] ? $_GET ['sortby'] : 'displayorder'; 
		
		if($_GET['userId']) {
			$maxposition = 0;
			$_G['forum_thread']['replies'] = C::t('forum_post')->count_by_tid_invisible_authorid($_G['tid'], $_GET['userId']);
			$_G['forum_thread']['replies']--;
			$onlyauthoradd = 1;
		}
		if($maxposition) {
			$_G['forum_thread']['replies'] = $maxposition - 1;
		}
		$_G['ppp'] = $_G['forum']['threadcaches'] && !$_G['uid'] ? $_G['setting']['postperpage'] : $_G['ppp'];
		$totalpage = ceil(($_G['forum_thread']['replies'] + 1) / $_G['ppp']);
		$page > $totalpage && $page = $totalpage;
		$_G['forum_pagebydesc'] = !$maxposition && $page > 2 && $page > ($totalpage / 2) ? TRUE : FALSE;
		
		if($maxposition) {
			$start = ($page - 1) * $_G['ppp'] + 1;
			$end = $start + $_G['ppp'];
			if($ordertype == 1) {
				$end = $maxposition - ($page - 1) * $_G['ppp'] + ($page > 1 ? 2 : 1);
				$start = $end - $_G['ppp'] + ($page > 1 ? 0 : 1);
				$start = max(array(1,$start));
			}
			$have_badpost = $realpost = $lastposition = 0;
			foreach(C::t('forum_post')->fetch_all_by_tid_range_position($posttableid, $_G['tid'], $start, $end, $maxposition, $ordertype) as $post) {
				if($post['invisible'] != 0) {
					$have_badpost = 1;
				}
				$cachepids[$post[position]] = $post['pid'];
				$postarr[$post[position]] = $post;
				$lastposition = $post['position'];
			}
			$realpost = count($postarr);
			if($realpost != $_G['ppp'] || $have_badpost) {
				$k = 0;
				for($i = $start; $i < $end; $i ++) {
					if(!empty($cachepids[$i])) {
						$k = $cachepids[$i];
						$isdel_post[$i] = array('deleted' => 1, 'pid' => $k, 'message' => '', 'position' => $i);
					} elseif($i < $maxposition || ($lastposition && $i < $lastposition)) {
						$isdel_post[$i] = array('deleted' => 1, 'pid' => $k, 'message' => '', 'position' => $i);
					}
					$k ++;
				}
			}
			$pagebydesc = false;
		}
		if(!$maxposition) {
			$postarr = C::t('forum_post')->fetch_all_common_viewthread_by_tid($_G['tid'], $visibleallflag, $_GET['userId'], $_G['forum_pagebydesc'], $ordertype, $_G['forum_thread']['replies'] + 1, $start_limit, ($_G['forum_pagebydesc'] ? $_G['forum_ppp2'] : $_G['ppp']));
		
		}
		if($page == 1 && $ordertype == 1) {
			$firstpost = C::t('forum_post')->fetch_threadpost_by_tid_invisible($_G['tid']);
			if($firstpost['invisible'] == 0 || $visibleallflag == 1) {
				$postarr = array_merge(array($firstpost), $postarr);
				unset($firstpost);
			}
		}
		
		
		$topicInstance = new topic();
		$postlist = $topicInstance->getQuoteImg($_G, $postarr);
		
		
		require_once libfile ( 'function/attachment' );
		$forumclass ->parseattach ( $tid, '', $postlist );
		require_once '../model/table/x25/table_common_member.php';

		$temp = ($page-1)*$limit+1;
		
		$sortid = C::t ( 'forum_typeoptionvar' )->fetch_all_by_tid_optionid ($_G['tid'],null);
		$fenlei_arr = $topicInstance->common_reply_oneself_fenlei($_G['tid']);
		
		/*foreach($postlist as $pkey=>$plist){
			$aaa[]=$pkey;
		}
		foreach($postlist as $akey=>$alist){
			$postlist[$aaa[0]][position]=1; 
		}*/
		$data_post = $topicInstance->parseTopic($_G, $postlist, $forumclass,$userId,$temp);
		
		$count = C::t ( 'forum_post' )->count_by_tid_dateline ( $posttableid, $tid, time () );
		$res = C::t ( 'forum_thread' )->increase ( $tid, array (
				'views' => 1
		) );
		if (! $res) {
			$obj -> rs = FAILED;
			echo echo_json($obj);
		}
		
		$thread = C::t('forum_thread')->fetch($_G['tid']);
		/*[vote topic]*/
		if($thread[special]==1){
			if($page==1){$data_post['topic']['type']=(Int)3;}
			$toupiao=array();
			$vote_query = DB::query("SELECT * FROM ".DB::table('forum_poll')." where tid=".$_G['tid']);
			while ($vote_result = DB::fetch($vote_query)) {
				$vote_arr[] = $vote_result;
			}
				
			$polloption_query = DB::query("SELECT polloption as name,polloptionid as poll_item_id,votes as total_num FROM ".DB::table('forum_polloption')." where tid=".$_G['tid']);
			while ($polloption_rst = DB::fetch($polloption_query)) {
				$polloption_arr[] = $polloption_rst;
			}
				
			for($di=0;$di<count($polloption_arr);$di++){
				$pollids=$polloption_arr[$di][poll_item_id]=(Int)$polloption_arr[$di][poll_item_id];
				$polloption_arr[$di][total_num]=(Int)$polloption_arr[$di][total_num];
			}
			
			$toupiao['deadline']=$vote_arr[0][expiration];
			$toupiao['is_visible']=$vote_arr[0][visible]==0?true:false;
			$toupiao['voters']=(Int)$vote_arr[0][voters];
			$toupiao['poll_id']=array("0"=>(Int)"");
			$toupiao['poll_item_list']=$polloption_arr;
			$toupiao['type']=$vote_arr[0][maxchoices]==0?(Int)1:(Int)$vote_arr[0][maxchoices];
				
			if(empty($accessSecret) || empty($accessToken))
			{
				$toupiao['poll_status']=2;
			}
			else
			{
				$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
				$uid =$arrAccess['user_id'];
				$pollvoter_query = DB::fetch(DB::query("SELECT dateline FROM ".DB::table('forum_pollvoter')." where tid=".$_G['tid']." and uid=".$uid));
				if(empty($pollvoter_query[dateline])){
					$toupiao['poll_status']=2;
				}else{
					if(time()-$pollvoter_query[dateline]>86400){
						$toupiao['poll_status']=3;
					}else{
						$toupiao['poll_status']=1;
					}
				}
			}
		}
		
		$N = ceil ( $_G['forum_thread']['replies'] / $limit );
		$data_post['has_next'] = ($page>=$N || $N==1) ?0:1; 
		$data_post ['page'] = (Int)$page;
		$data_post ['total_num'] = ( int ) $_G['forum_thread']['replies'];
		$data_post ['img_url'] = '';
		if($thread[special]==1){
			$data_post['topic']['poll_info']=$toupiao;
		}
		$data_post ['icon_url'] = DISCUZSERVERURL;
		$data_post ['rs'] = 1;
		if (empty ( $data_post ['list'] )) {
			$data_post ['list'] = array();
		}
		/*
		$ct=$data_post['topic']['content'];
		$message=!empty($fenlei_arr)?array_merge($fenlei_arr,$ct):$ct;
		$data_post['topic']['content']=$message;
		*/
		return $data_post;
	}

}

?>