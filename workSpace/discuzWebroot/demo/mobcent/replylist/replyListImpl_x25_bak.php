<?php

require_once './abstarctReplyList.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../source/function/function_threadsort.php';
require_once '../../config/config_ucenter.php';
require_once ('../../source/discuz_version.php');
require_once '../tool/tool.php';
require_once '../tool/img_do.php';
require_once '../Config/public.php';
require_once '../model/table/x25/forum.php';
require_once '../model/table/x25/table_common_member.php';
require_once '../model/table/x25/table_forum_typeoptionvar.php';
require_once '../model/table/x25/table_forum_threadtype.php';
require_once '../model/table/x25/table_forum_thread.php';
require_once '../model/table/x25/table_surround_user.php';
require_once '../model/table/x25/table_common_member_profile.php';
require_once '../tool/Thumbnail.php';
require_once '../Config/public.php';
require_once '../model/table/x25/topic.php';
define('ALLOWGUEST', 1);  
C::app ()->init ();
require_once libfile ( 'function/forumlist' );
require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'function/attachment' );
require_once libfile ( 'function/post' );
require_once '../tool/constants.php';  
require_once '../public/mobcentDatabase.php';
 
class replyListImpl_x25 extends abstarctReplyList {
	public function getReplyList() {
		
		$_G['forum_attachpids'] = array();
		
		$discuz_sever_url = DISCUZSERVERURL;
		$posttableid = '';
		$_G ['tid'] = $tid = $_GET ['topicId'] ;
		$_G ['fid'] = $fid = $_GET ['boardId'] ;
		$forumclass = new forum();
		$forumclass->loadmobcentforum($tid, $fid);
		
		$_G['forum'] = $forumclass->loadforum($tid,$fid);
		$info = new mobcentGetInfo();
		$accessSecret = empty($_GET['accessSecret'])?'':$_GET['accessSecret'];
		$accessToken = empty($_GET['accessToken'])?'':$_GET['accessToken'];
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
			$uid = $_G ['uid'] =$arrAccess['user_id'];
			if(empty($uid))
			{
				return $info -> userAccessError();
				exit();
			}
			$group = $info-> sel_group_by_uid($uid);
		}
		
		if(!$group['readaccess']) 
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '01110001';
			return $data_post;
			exit();
		}
		runhooks();
		$ordertype = $_GET ['order'] ? $_GET ['order'] : 2;  
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20;
		
 
		$order = $_GET ['sortby'] ? $_GET ['sortby'] : 'displayorder';  
 
		if($_G['forum_thread']['maxposition']) {
			$maxposition = $_G['forum_thread']['maxposition'];
		} else {
			$maxposition = C::t('forum_post')->fetch_maxposition_by_tid($posttableid, $_G['tid']);
		}
		if($maxposition) {
			$_G['forum_thread']['replies'] = $maxposition - 1;
		}
		if($maxposition) {
			if($page ==1)
			{
				$start =1;
			}else {
				$start = ($page -1)*$limit+1;
			}
			$end = $limit;
			
			if($ordertype == 1) {
				if($page ==1)
				{
					$start =0;
				}else {
					$start = ($page -1)*$limit;
				}
			}
			$have_badpost = $realpost = $lastposition = 0;
			$postarr = C::t('forum_post')->fetch_all_common_viewthread_by_tid($_G['tid'], $visibleallflag, $_GET['authorid'],0, $ordertype, $maxposition + 1, $start, $end);
			$realpost = count($postarr);
			if($realpost != $limit || $have_badpost) {
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
		
	 
	
		if($page == 1) {
			$firstpost = C::t('forum_post')->fetch_threadpost_by_tid_invisible($tid);
			
			if($firstpost['invisible'] == 0 || $visibleallflag == 1) {
				$postarr = array_merge(array($firstpost), $postarr);
				unset($firstpost);
			}
		}
		
		
		$topicInstance = new topic();
		$postlist = $topicInstance->getQuoteImg($_G, $postarr);
		
		require_once libfile ( 'function/attachment' );
		$forumclass ->parseattach ( $tid, '', $postlist );
		 
		if($page ==1)
		{
			$temp = ($page-1)*$limit;
		}
		$temp = ($page-1)*$limit+1;
		
 
		/*rx 20130816 debug hui fu hou ke jian yin cang nei rong*/
		foreach($postlist as $rxk=>$rxv){
			if(strstr($rxv[message], "hide")!=""){
				$postlist[$rxk][message]=str_replace("[hide]", "\r\n([hide]", $postlist[$rxk][message]);
				$postlist[$rxk][message]=str_replace("[/hide]", "[/hide])\r\n", $postlist[$rxk][message]);
			}
			$authorids[]=$postlist[$rxk][authorid];
		}
		if(in_array($uid, $authorids)){
			foreach($postlist as $rxk=>$rxv){
				if(strstr($rxv[message], "hide")!=""){
					$hiddenword=Common::get_web_unicode_charset('\u9690\u85cf\u5185\u5bb9\uff1a\uff1a');
					$postlist[$rxk][message]=str_replace("[hide]", "", $postlist[$rxk][message]);
					$postlist[$rxk][message]=str_replace("[/hide]", "", $postlist[$rxk][message]);
				}
			}
		} 
		/*end rx 20130816*/ 
		
		$sortid = C::t ( 'forum_typeoptionvar' )->fetch_all_by_tid_optionid ($_G['tid'],null);
		$fenlei_arr = $topicInstance->common_reply_oneself_fenlei($_G['tid']);
		$data_post = $topicInstance->parseTopic($_G, $postlist, $forumclass,$uid,$temp);
		
		/*rx 20130816*/
		if($page==1){
			foreach($data_post[topic][content] as $data_key=>$data_val){
				$data_post[topic][content][$data_key][infor]=$topicInstance->replaceHtmlAndJs($data_val[infor]);
			}
 		} 
		/*end rx 20130816*/
		
		if(!empty($fenlei_arr)){
			/*rx 2013-7-10 debug*/
			$sort_arrs = C::t ( 'forum_typeoptionvar' )->fetch_all_by_tid_optionid ($tid,null);
			foreach($sort_arrs as $soarr){
				$soimg=unserialize($soarr[value]);
				$sopic[]=str_replace("data/attachment/forum", "/mobcent/data/attachment/forum/mobcentSmallPreview", $soimg[url])  ;
			}
			foreach($data_post[topic][content] as $dkey=>$dval){
				foreach($sopic as $sp){
					if($dval[type]==1 && $dval[infor]==$sp){
						unset($data_post[topic][content][$dkey]);
						unset($data_post[topic][content][$dkey+1]);
					}
				}
			} 
			/*end rx debug*/
		}
		
		$thread = C::t('forum_thread')->fetch($_G['tid']);
		$fenleitype=$_G['forum']['threadtypes']['types'][$thread['typeid']];
		
		if($page==1){
			$data_post['topic']['type']=(Int)1;
			$data_post['topic']['flag']=(Int)0;
		
			if(!empty($sortid)){
				/*[fen lei ming cheng]*/
				$fenlei_name= C::t ( 'forum_threadtype' )->fetch_name_by_typeid ($sortid[0]['sortid']);
				if($fenlei_name[0][name]!=""){
					$data_post['topic']['title']="[".$topicInstance->replaceHtmlAndJs($fenlei_name[0][name])."]".$data_post['topic']['title'];
					$data_post['topic']['type']=(Int)2;
				} 
			}
			/*[end fen lei ming cheng]*/
			
			/*[lei bie ming cheng]*/
			if(!empty($fenleitype)){
				if($fenleitype!=""){
					$data_post['topic']['title']="[".$topicInstance->replaceHtmlAndJs($fenleitype)."]".$data_post['topic']['title'];
					$data_post['topic']['flag']=(Int)1;
				} 
			}
			/*[end lei bie ming cheng]*/
		}
		
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
		/*[end vote topic]*/
		
		
		if($data_post['errcode']== '01040007')
		{
			$data ['rs'] = 0;
			$data ['errcode'] = '01040007';
			return $data;exit();
		}else{ 
			$count = C::t ( 'forum_post' )->count_by_tid_dateline ( $posttableid, $tid, time () );
			$res = C::t ( 'forum_thread' )->increase ( $tid, array (
					'views' => 1
			) );
			if (! $res) {
				$data_post ['rs'] = 0;
				return $data_post;
			}
		
			$N = ceil ( ($count-1) / $limit );
			$data_post['has_next'] = ($page>=$N || $N==1) ?0:1;  
			$data_post ['page'] = (Int)$page;
			$data_post ['total_num'] = ( int ) $count - 1;
			$data_post ['img_url'] = '';
			$data_post ['icon_url'] = DISCUZSERVERURL;
			$data_post ['rs'] = 1;
			if (empty ( $data_post ['list'] )) {
				$data_post ['list'] = array();
			}
			if($page ==1){
				$ct=$data_post['topic']['content'];
				$message=!empty($fenlei_arr)?array_merge($fenlei_arr,$ct):$ct;
				$data_post['topic']['content']=$message;
				if($thread[special]==1){
					$data_post['topic']['poll_info']=$toupiao;
				}
			}
			
			return $data_post;
			
		}
	   
	}
	
}

?>