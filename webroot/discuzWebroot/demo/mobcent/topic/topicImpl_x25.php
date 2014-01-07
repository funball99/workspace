<?php
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../source/function/function_threadsort.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../model/table/x25/forum.php';
require_once '../tool/Thumbnail.php';
define('ALLOWGUEST', 1);
$discuz_sever_url = DISCUZSERVERURL;
C::app ()->init ();
require_once libfile ( 'function/forumlist' );
require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'function/attachment' );
require_once libfile ( 'function/post' );
require_once ('./abstractTopic.php');
require_once '../model/table/x25/topic.php';
require_once '../public/mobcentDatabase.php';

class topicImpl_x25 extends abstractTopic {
	function getTopicObj() {
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
		$tid = $_G['tid'] = $_GET ['topicId'];  
		$_G['fid'] = $fid = $_GET ['boardId'] ; 
		$forumclass = new forum();
		$forumclass->loadmobcentforum($tid, $fid);
		runhooks();
		
		$post = DB::fetch_first ( 'SELECT * FROM %t as a left join %t as b USING(tid) WHERE tid=%d AND POSITION=1 limit 1', array (
				'forum_post',
				'forum_thread',
				$tid
		) );
		$arr = C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$post['tid'], 'pid', $post['pid']);
		
		if (empty ( $post ['tid'] )) {
			$data_post['rs'] = 0;
			$data_post['errcode'] = "01040007";
			return $data_post;
		}
		
		$post ['message'] = discuzcode ( $post ['message'], $post ['smileyoff'], $post ['bbcodeoff'] );
		$post ['message'] =str_replace('<img src="static/', '<img src="/static/', $post ['message']);
		
		if ($post ['attachment']) {
			$res = preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i", $post ['message'], $matches );
			$tableid = substr($post['tid'],-1,1);
			$aids = $matches['1'];
			$matches = array_filter($matches);
			foreach(C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$post['tid'], 'pid', $post['pid']) as $attach) {
							$Allaids[] = $attach['aid'];
			}
			if(count($aids) !== count($Allaids))
			{
				$res = true;
				$attachment = C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$post['tid'], 'pid', $post['pid']);
				$arr = array_keys($attachment);
				
				foreach ($arr as $key=>$val)
				{
					if(in_array($val, $aids))
					{
						continue;
					}
					$matches = array( 0=>array('[attach]'.$val.'[/attach]'),1=>array($val));
					$res = preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i", $post ['message'], $matches );
				}
			}
			else
			{
				$attachment = C::t('forum_attachment_n')->fetch_all($tableid, $aids);
			}
			foreach($attachment as $v){
				$data_imgs[$v['aid']]['attachment'] = $v['attachment'];
				$data_imgs[$v['aid']]['dateline'] = $v['dateline'];
			}
			$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
			$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
			
			if ($res) {
				$picNew = new topic();
				foreach ( $matches [1] as $k => $v ) {
					$filename = $picNew->parseTradeTopicImg($data_imgs[$v]);
					$imgs [] = '<img src="' .$filename. '" />';
					$attachmentId = $v;
				}
			}
			foreach ( $matches [0] as $k => $v ) {
				$pattern = "/\[attach\](\d+)\[\/attach\]/i";
			}
			
			$res = preg_match_all ( $pattern, $post ['message'], $message_val );
			
			if ($res) {
				foreach ( $message_val [1] as $k => $v ) {
					$pattern = "/\[attach\]" . $v . "\[\/attach\]/i";
					$post ['message'] = preg_replace ( $pattern, $imgs [$k], $post ['message'] );
				}
			}
			$img_url = $post ['attachments'] [$attachmentId] ['url'];
		}
		
		$postlist = $forumclass->viewthread_procpost($post, $_G['member']['lastvisit'], 2, 2);
		$topicContent = text_replace($postlist ['message']);
		$topicContent = discuzcode ( $topicContent, $post ['smileyoff'], $post ['bbcodeoff'] );
		$topicContent = str_replace('<img src="static/', '<img src="/static/', $topicContent);
		
		$post ['message'] = text_replace($topicContent);
		$post ['message'] = preg_replace( "/\<font class=\"jammer\">.+\<\/font>/i",'' ,$post ['message']);
		$post ['message'] = preg_replace( "/\<span style=\"display:none\">.+\<\/span>/i",'' ,$post ['message']);
		
		$topicInstance = new topic();
	
		$message = $topicInstance ->parseTradeTopic($_G,$post);
		
		$data_post = $tags = array ();
		$message1 = doContent ( $post ['message'] );

	
		$message2 = getContentFont ( $post ['message'] );
		foreach($message1 as $k=>$v){
			if($v['type']==0){
				unset($message1[$k]);
			}else{
		
			}
		}
		$message_array2 = explode('|~|', $message2);
		foreach($message_array2 as $k=>$v){
			if(!empty($v))
			{
				$message[]=array("infor" =>str_replace('<hr class="l" />','',preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2", $v)),"type"	=>0,);
			}
			
			if(preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$message1[$k]["infor"])){
				$message[]=$message1[$k];
					
			}
		
		}
		
		
		
		$sortid = C::t ( 'forum_typeoptionvar' )->fetch_all_by_tid_optionid ($_G['tid'],null);
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
		$message=!empty($fenlei_arr)?array_merge($fenlei_arr,$message):$message;
		
		$data_post ['img_url'] = '';
		$data_post ["rs"] = 1;
		$data_post ['topic_content'] = $message;
		return $data_post;
	}

}

?>