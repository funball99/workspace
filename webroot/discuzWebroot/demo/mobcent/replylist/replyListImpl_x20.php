<?php
require_once './abstarctReplyList.php';
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../tool/img_do.php';
require_once '../Config/public.php';
require_once '../model/table/x20/forum.php';
require_once '../tool/Thumbnail.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../source/function/function_forumlist.php';
require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'function/attachment' );
require_once libfile ( 'function/post' );
require_once '../tool/constants.php';  
require_once '../model/table/x20/mobcentDatabase.php';
require_once '../model/table/x20/table_forum_thread.php';
require_once '../model/table/x20/topic.php';
require_once '../../source/function/function_threadsort.php';

class replyListImpl_x20 extends abstarctReplyList {
	public function getReplyList() {
		$_G ['tid'] = $tid = $_GET ['topicId'] ;
		$_G ['fid'] = $fid = $_GET ['boardId'] ;
		
		$forumclass = new forum();
		$posttable = 'forum_post';
		$_G['forum_attachpids'] = array();
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
			$query = $info-> sel_QQuser($qquser);
			while($arr = DB::fetch($query))
			{
				$group =$arr;
			}
		}
		else
		{
			$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
			$_G['uid'] = $userId = $arrAccess['user_id'];
			if(empty($userId))
			{
				return C::t('common_member') -> userAccessError();
				exit();
			}
			$group = $info-> sel_group_by_uid($userId);
		}
			if(!$group['readaccess']) 
			{
				$data_post['rs'] = 0;
				$data_post['errcode'] = '01110001';
				return $data_post;
				exit();
			}
		$discuz_sever_url = DISCUZSERVERURL;
		$posttableid = ''; 
		$_G['tid'] = $tid = $_GET ['topicId'] ; 
		$_G['fid'] = $fid = $_GET ['boardId'] ; 
		
		runhooks();
		$ordertype = $_GET ['order'] ? $_GET ['order'] : 2; 
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20;
		$start = ($page <= 1) ? 0 : (($page - 1) * $limit); 
		$order = $_GET ['sortby'] ? $_GET ['sortby'] : 'displayorder'; 
		$maxposition = $count = C::t ( 'forum_post' )->count_by_tid_dateline ( $posttableid, $tid, time () );
		if($ordertype != 1) {
			$pageadd = " ORDER BY p.dateline LIMIT ". $start .',' .$limit;
		} else {
			$_G['forum_numpost'] = $_G['forum_thread']['replies'] + 2 - $_G['forum_numpost'] + ($page > 1 ? 1 : 0);
			$pageadd = " ORDER BY p.first DESC, p.dateline DESC LIMIT ". $start .',' .$limit;
		}
		$have_badpost = $realpost = $lastposition = 0;
		$q2 = DB::query("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$_G ['tid']." AND p.invisible=0 AND p.first !=1". $pageadd);
		$realpost = $lastposition = 0;
		$num =0;
		while ($post = DB::fetch($q2)) {
		
			if($post['invisible'] != 0) {
				$have_badpost = 1;
			}
			$cachepids[$post[pid]] = $post['pid'];
			$postarr[$post[pid]] = $post;
			$lastposition = $num;
			$num ++;
		}
		$First_query = DB::query("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$_G ['tid']." AND p.first =1");
		global $_G;
		
		while ($rows = DB::fetch($First_query)) {
			foreach($_G['cache']['smilies']['searcharray'] as $key=>$val)
			{
				$rows['message'] = preg_replace($val, "[$key]", $rows['message']);
			}
			$data[$rows['pid']] = $rows;
		} 
		
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
		
		$topicInstance = new topic();
		$postlist = $topicInstance->getQuoteImg($_G, $postarr);
		foreach ( $postarr as $uid => $post ) {
			$postusers[$post['authorid']] = array();
			$quotemessage = '';
			if (strstr ( $post ['message'], '[quote]' ) != '') {
				$res = preg_match ( '\[color=#\d+\](.*)\[/color\]', $post ['message'], $quote ); 
				$postarr [$uid] ['quote_pid'] = $quote ['1'] [0];
				$postarr [$uid] ['is_quote'] = ( bool ) true;
				$postarr [$uid] ['message'] = preg_replace ( '#\[quote\][.\n\S\s]+\[/quote\]#', '', $post ['message'] );
			 
				$quotemessage = $topicInstance->parseQuoteMessage($post ['message']);
			} else {
				$postarr [$uid] ['is_quote'] = ( bool ) false;
			} 
			
			$post = array_merge ( $postarr [$uid], ( array ) $postusers [$post ['authorid']] );
			global $_G;
			foreach($_G['cache']['smilies']['searcharray'] as $key=>$val)
			{
				$post ['message'] = preg_replace($val, "[$key]", $post ['message']);
			}
			$post ['message'] = discuzcode ( $post ['message'], $post ['smileyoff'], $post ['bbcodeoff'] );
			$post ['message'] = str_replace('<img src="static/', '<img src="/static/', $post ['message']);
			$post ['authortitle'] = $_G ['cache'] ['usergroups'] [$post ['groupid']] ['grouptitle'];
			$post ['quotemessage'] = $quotemessage;
			if($post ['attachment'] == 2)
				$pids [] = $post ['pid'];
		
			$postlist [$post ['pid']] = $post;
		}
		
		
		$pids = implode(',',$pids);
		require_once libfile ( 'function/attachment' );
		$forumclass->parseattach ( $tid,$pids, '', $postlist );		
		/*renxing modified 2013-5-14 10:05:53*/
		if($page ==1)
		{
			if($postlist){
				$postlist = array_merge($data,$postlist);
			}else{
				$postlist = $data;
			}			
			$temp = ($page-1)*$limit;
		}else{ 
			$temp = ($page-1)*$limit+1;
		}
 
		$sort_query = DB::query("SELECT * FROM ".DB::table('forum_typeoptionvar')." WHERE tid=".$_G ['tid']);
		while ($sort_result = DB::fetch($sort_query)) {
			$sortid[] = $sort_result;
		}
		$threadsortshow = threadsortshow($sortid[0]['sortid'], $_G['tid']);
		foreach($threadsortshow[optionlist] as $opt){
			$opt_arr[]=$opt;
		}
		$fenlei="";
		foreach($opt_arr as $v1){
			$fenlei.=$v1['title'].':'.$v1['value']."\r\n";
		}
		
		$fenlei=str_replace("&raquo;", "", $fenlei);
		$fenlei=str_replace("&nbsp;", " ", $fenlei);
		$fenlei=str_replace('onload="thumbImg(this)"', '', $fenlei);
		$fenlei=str_replace('data/attachment/forum', '/mobcent/data/attachment/forum/mobcentSmallPreview', $fenlei);
		$fenlei=trim(str_replace('border="0"', 'width="2" height="4" /', $fenlei));
		
		$fenlei1 = doContent ($fenlei);
		$fenlei2 = getContentFont ($fenlei);
		
		foreach($fenlei1 as $k=>$v){
			if($v['type']==0){
				unset($fenlei1[$k]);
			}else{
		
			}
		}
		
		$fenlei_array2 = explode('|~|', $fenlei2);
		foreach($fenlei_array2 as $k=>$v){
			if(!empty($v)){
				$fenlei_arr[]=array("infor" =>str_replace('<hr class="l" />','',preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2", $v)),"type"	=>0,);
			}
		
			if(preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$fenlei1[$k]["infor"])){
				$fenlei_arr[]=$fenlei1[$k];
			}
		}
		/*rx 20130820 debug hui fu hou ke jian yin cang nei rong*/
		if(!empty($_GET['accessSecret']) && !empty($_GET['accessToken'])){
			$user_query = DB::query("SELECT credits FROM ".DB::table('common_member')." where uid=".$userId);
			while ($user_list = DB::fetch($user_query)) {
				$user_arr[] = $user_list;
			}
			$user_jifen=$user_arr[0][credits];
		}else{
			$user_jifen=0;
		}
		foreach($postlist as $rxk=>$rxv){
			$authorids[]=$postlist[$rxk][authorid];
		}
		foreach($postlist as $rxk=>$rxv){
			$create_time=$rxv[dateline];
			if(strstr($rxv[message], "hide")!=""){
				/*echo $postlist[$rxk][message];exit;*/
				$postlist[$rxk][message]=str_replace("[/hide]", "[/hide]*", $postlist[$rxk][message]);
				$msg[]=explode("*", $postlist[$rxk][message]);
				/*print_r($msg);exit;*/
				for($mx=0;$mx<count($msg[0]);$mx++){
					$m1=strstr($msg[0][$mx], "[hide");
					$t1=strpos($m1,"=");
					$s1=strpos($m1,",");
					$s2=strpos($m1,"]");
					/*echo $m1.'@'.$t1.'@'.$s1.'@'.$s2.'<br>';*/
					if(!empty($t1)){
						if(substr($m1,6,1)!="d"){ /*ji fen '[hide=50]aaa[/hide]'*/
							$rs1=substr($m1,6,($s2-6)); /*JiFenYaoQiu*/
							$f1="[hide=".$rs1."]";
							if($user_jifen > $rs1){
								$msg[0][$mx]=str_replace($f1, "(",$msg[0][$mx]);
								$msg[0][$mx]=str_replace("[/hide]", ")",$msg[0][$mx]);
							}else{
								$msg[0][$mx]=str_replace($f1, "\r\n(".$f1,$msg[0][$mx]);
								$msg[0][$mx]=str_replace("[/hide]", "[/hide])\r\n",$msg[0][$mx]);
							}
						}else{ 
							if(!empty($s1)){ /*ji fen + tian shu '[hide=d5,80]aaa[/hide]'*/
								$sj0=substr($m1, 7,($s1-7));  
								$sj1=$sj0*86400; /*YouXiaoQiXian(miao)*/
								$rs2=substr($m1,($s1+1),($s2-$s1-1)); //JiFenYaoQiu
								$f2="[hide=d".$sj0.",".$rs2."]";
								if(time() - $create_time < $sj1){
									if($user_jifen > $rs2){
										$msg[0][$mx]=str_replace($f2, "(",$msg[0][$mx]);
										$msg[0][$mx]=str_replace("[/hide]", ")",$msg[0][$mx]);
									}else{
										$msg[0][$mx]=str_replace($f2, "(",$msg[0][$mx]);
										$msg[0][$mx]=str_replace("[/hide]", ")",$msg[0][$mx]);
									}
								}else{
									$f2="[hide=d".$sj0.",".$rs2."]";
									$msg[0][$mx]=str_replace($f2, "\r\n(".$f2,$msg[0][$mx]);
									$msg[0][$mx]=str_replace("[/hide]", "[/hide])\r\n",$msg[0][$mx]);
								}
							}else{ /*hui fu + tian shu '[hide=d5]aaa[/hide]'*/
								$sj0=substr($m1, 7,($s2-7));  
								$sj1=$sj0*86400; /*YouXiaoQiXian(miao)*/
								$f2="[hide=d".$sj0."]";
								if(time() - $create_time < $sj1){
									if(in_array($uid, $authorids)){
										$msg[0][$mx]=str_replace($f2, "(",$msg[0][$mx]);
										$msg[0][$mx]=str_replace("[/hide]", ")",$msg[0][$mx]);
									}else{
										$msg[0][$mx]=str_replace($f2, "\r\n(".$f2,$msg[0][$mx]);
										$msg[0][$mx]=str_replace("[/hide]", "[/hide])\r\n",$msg[0][$mx]);
									}
								}else{
									$f2="[hide=d".$sj0."]";
									$msg[0][$mx]=str_replace($f2, "(",$msg[0][$mx]);
									$msg[0][$mx]=str_replace("[/hide]", ")",$msg[0][$mx]);
								}
							}
						}
					}else{ /*hui fu '[hide]aaa[/hide]'*/
						$f3="[hide]";
						if(in_array($uid, $authorids)){
							$msg[0][$mx]=str_replace($f3, "(",$msg[0][$mx]);
							$msg[0][$mx]=str_replace("[/hide]", ")",$msg[0][$mx]);
						}else{
							$msg[0][$mx]=str_replace($f3, "\r\n(".$f3,$msg[0][$mx]);
							$msg[0][$mx]=str_replace("[/hide]", "[/hide])\r\n",$msg[0][$mx]);
						}
					} 
				}
				$postlist[$rxk][message]=implode("", $msg[0]);
			}
		}
		/*end rx 20130820*/ 
		$data_post = $topicInstance->parseTopic($_G, $postlist, $forumclass,$temp,$userId);
		
		/*
		if(isset($data_post['list'])){
			for($sd=0;$sd<count($data_post['list']);$sd++){
				$url = $data_post['list'][$sd][icon];
				$fileExists = @file_get_contents($url, null, null, -1, 1) ? true : false;
				if (!$fileExists) {
					$data_post['list'][$sd][icon]="";
				}
			}
		}*/
 		
		if(isset($data_post['list'])){
			for($sd=0;$sd<count($data_post['list']);$sd++){
				if (!checkRemoteFileExists($data_post['list'][$sd][icon])) {
					$data_post['list'][$sd][icon]="";
				}
			}
		}
		
		/*rx 20130816*/
		if($page==1){
			foreach($data_post[topic][content] as $data_key=>$data_val){
				$data_post[topic][content][$data_key][infor]=$topicInstance->replaceHtmlAndJs($data_val[infor]);
			}
		}
		/*end rx 20130816*/
		
		$thread = C::t('forum_thread')->fetch($_G['tid']);
		$fenleitype=$_G['forum']['threadtypes']['types'][$thread['typeid']];
		
		if($page==1){
			$data_post['topic']['type']=(Int)1;
			$data_post['topic']['flag']=(Int)0;
			if(!empty($sortid)){
				$fenlei_name= C::t ( 'forum_threadtype' )->fetch_name_by_typeid ($sortid[0]['sortid']);
				if($fenlei_name[name]!=""){
					$data_post['topic']['title']="[".$topicInstance->replaceHtmlAndJs($fenlei_name[name])."]".$topicInstance->replaceHtmlAndJs($data_post['topic']['title']);
					$data_post['topic']['type']=(Int)2;
				}
			}
			if(!empty($fenleitype)){
				if($fenleitype!=""){
					$data_post['topic']['title']="[".$topicInstance->replaceHtmlAndJs($fenleitype)."]".$data_post['topic']['title'];
					$data_post['topic']['flag']=(Int)1;
				}
			}
		}
		
		for($i=0;$i<count($data_post['list']);$i++){
			if($data_post['list'][$i]['is_quote']!=""){				 	
				$a=preg_replace ('[url=/mobcent/download/down.php]', '', $postlist[$i+1][quotemessage]);
				$b=preg_replace ('[/url]', '', $a);
				$c=preg_replace ('#\[]#', '', $b);
				$data_post['list'][$i]['quote_content']=$c;				 
			}
		} 
		 
		
		/*[vote topic renxing 2013-7-9]*/
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
		}
		else
		{
			$res = $forumclass->viewthread_updateviews('forum_thread',$tid);
			if ($res == false) {
				$data_post['rs'] = 0;
				return $data_post;
				exit();
			}
			
			$N = ceil ( $count / $limit );
			$data_post['has_next'] = ($page>=$N || $N==1) ?0:1; 
			$data_post ['page'] = (Int)$page;
			$data_post ['total_num'] = ( int ) ($count - 1);
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