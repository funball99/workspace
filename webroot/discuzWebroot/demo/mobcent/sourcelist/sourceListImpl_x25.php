<?php
require_once './abstracSourceList.php';
define('IN_MOBCENT',1);
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
C::app ()->init();
require_once '../public/mobcentDatabase.php';
require_once '../model/table/x25/table_forum_thread.php';
require_once '../model/table/x25/topic.php';
require_once '../tool/Thumbnail.php';
require_once '../model/table/x25/table_common_block.php';
require_once '../model/table/x25/table_add_portal_module.php';
require_once '../model/table/x25/table_portal_article_count.php';
require_once libfile ( 'function/discuzcode' );
class sourceListImpl_x25 extends abstracSourceList {
	public function getSourceListImplObj() {
		$xm=new topic();
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20;
		$start_limit = ($page - 1) * $limit;
		$moduleId = $_GET ['moduleId'];
		$s=file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
		$pic=file_exists('../manage_x25/AddPic.xml')?join("",file('../manage_x25/AddPic.xml')):array();
		$result =$xm->xml_to_array($s);
		$picResult = $xm->xml_to_array($pic);
		
		$setting_list = DB::query("SELECT * FROM ".DB::table('common_setting'));
		while($setting_value = DB::fetch($setting_list)) {
			$setting[] = $setting_value;
		}
		foreach($setting as $st){
			if($st[skey]=='ftp'){
				$myval=unserialize($st[svalue]);
				$ftp_isopen=$myval[on];
				$ftp_host=$myval[host];
				$ftp_attachurl=$myval[attachurl];
			}
		}
		 
		if($result['version'][0][0]==2){
			if($moduleId==0){
				$Img_list=DB::query(" SELECT * FROM ".DB::table('add_portal_module')." where isimage=1 ORDER BY display desc limit 0,5");
				while($Img_value = DB::fetch($Img_list)) {
					$ImgArr[] = $Img_value;
				}
				//print_r($ImgArr);exit;
				foreach($ImgArr as $key=>$val)
				{  
					if($val['cidtype'] =='tid')
					{ 
						$val[cid] = is_numeric($val[cid])?$val[cid]:-1;
						$thread = get_thread_by_tid($val[cid]);
						$arr = DB::fetch(DB::query("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$val[cid]." AND p.first =1"));
						if(!empty($arr)){
							$data ['board_id'] = ( int ) $thread ['fid'];
							$data ['topic_id'] = ( int ) $val['cid'];
							$data ['article_id'] = ( int ) 0;
							$data ['title'] = Common::get_unicode_charset($val[title]==""?$thread['subject']:$val[title]);
							$data ['user_id'] = ( int ) $thread ['authorid'];
							$data ['last_reply_date'] = ($thread ['lastpost']) . "000";
							$data ['user_nick_name'] =Common::get_unicode_charset('\u533f\u540d');
							$data ['hits'] = ( int ) $thread ['views'];
							$data ['subject'] = empty($arr[0]['message'])?"":sub_str($arr[0]['message'],0,40);
							$data ['replies'] = ( int ) $thread ['replies'];
							$topicInstance = new topic();
							if (!empty($val['imgval']) &&  $val['isimage']==1 ) { 
								if($val['imgtype']==""){
									$data ['pic_path'] = $val['imgval'];
								}elseif($val['imgtype']=="tid"){
									$val['imgval'] = is_numeric($val['imgval'])?$val['imgval']:-1;
									$threadtid = get_thread_by_tid($val[imgval]);
									//$pic = C::t('forum_thread') ->fetch_all_threadimage($threadtid);
									$query = DB::query ( "SELECT B.*,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $threadtid['tid']);
									$pic = DB::fetch($query);
									$data ['pic_path'] = $topicInstance->parseTargeImage($pic);
								}elseif($val['imgtype']=="aid"){
									$val['imgval'] = is_numeric($val['imgval'])?$val['imgval']:-1;
									$aid_query = DB::query("SELECT content FROM ".DB::table('portal_article_content')." WHERE aid=".$val['imgval']);
									while ($aid_list = DB::fetch($aid_query)) {
										$aid_arr[] = $aid_list;
									}
									preg_match_all("/<img.*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i",str_ireplace("\\","",$aid_arr[0][content]),$aidarrs);
									$data ['pic_path'] = $aidarrs[1][0];
								}
							}else{
								$data ['pic_path'] = "";
							}
							$picItemdata[]=$data; 
						}
					}else if ($val['cidtype'] =='aid'){ 
						$val[cid] = is_numeric($val[cid])?$val[cid]:-1;
						$portal = DB::fetch(DB::query("SELECT * FROM ".DB::table('portal_article_title')." where aid=".$val[cid]));
						if(!empty($portal)){
							$data ['board_id'] = ( int )0;
							$data ['topic_id'] = ( int )0;
							$data ['article_id'] = ( int ) $val['cid'];
							$data ['title'] = Common::get_unicode_charset($val[title]==""?$portal[0]['title']:$val[title]);
							$data ['user_id'] = ( int ) $portal[0]['uid'];
							$data ['last_reply_date'] = ($portal[0] ['dateline'])."000";
							$data ['user_nick_name'] = $portal[0]['username'];
							$data ['hits'] = 0;
							$data ['subject'] = empty($thread ['summary'])?"":sub_str($thread ['summary'],0,40);
							$data ['replies'] = 0;
							$topicInstance = new topic();
							if (!empty($val['imgval']) &&  $val['isimage']==1 ) {
								if($val['imgtype']==""){
									$data ['pic_path'] = $val['imgval'];
								}elseif($val['imgtype']=="tid"){
									$val['imgval'] = is_numeric($val['imgval'])?$val['imgval']:-1;
									$threadtid = get_thread_by_tid($val[imgval]);
									//$pic = C::t('forum_thread') ->fetch_all_threadimage($threadtid);
									$query = DB::query ( "SELECT B.*,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $threadtid['tid']);
									$pic = DB::fetch($query);
									$data ['pic_path'] = $topicInstance->parseTargeImage($pic);
										
								}elseif($val['imgtype']=="aid"){
									$val['imgval'] = is_numeric($val['imgval'])?$val['imgval']:-1;
									$aid_query = DB::query("SELECT content FROM ".DB::table('portal_article_content')." WHERE aid=".$val['imgval']);
									while ($aid_list = DB::fetch($aid_query)) {
										$aid_arr[] = $aid_list;
									}
									preg_match_all("/<img.*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i",str_ireplace("\\","",$aid_arr[0][content]),$aidarrs);
									$data ['pic_path'] = $aidarrs[1][0];
								}
							}else{
								$data ['pic_path'] = "";
							}
							$picItemdata[]=$data;
						}
					}else{
						$data ['board_id'] = ( int )0;
						$data ['topic_id'] = ( int )0;
						$data ['article_id'] = ( int ) $val['cid'];
						$data ['title'] = Common::get_unicode_charset($val['title']);
						$data ['user_id'] = ( int ) 0;
						$data ['last_reply_date'] =  "000";
						$data ['user_nick_name'] = array();
						$data ['hits'] = ( int ) 0;
						$data ['subject'] = array();
						$data ['replies'] = ( int ) 0;
						$data ['pic_path'] = $val['imgval'];
							
						$topicInstance = new topic();
						if (!empty($val['imgval']) &&  $val['isimage']==1 ) {
							if($val['imgtype']==""){
								$data ['pic_path'] = $val['imgval'];
							}elseif($val['imgtype']=="tid"){
								$val['imgval'] = is_numeric($val['imgval'])?$val['imgval']:-1;
								$threadtid = get_thread_by_tid($val[imgval]);
								//$pic = C::t('forum_thread') ->fetch_all_threadimage($threadtid);
								$query = DB::query ( "SELECT B.*,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $threadtid['tid']);
								$pic = DB::fetch($query);
								$data ['pic_path'] = $topicInstance->parseTargeImage($pic);
							}elseif($val['imgtype']=="aid"){
								$val['imgval'] = is_numeric($val['imgval'])?$val['imgval']:-1;
								$aid_query = DB::query("SELECT content FROM ".DB::table('portal_article_content')." WHERE aid=".$val['imgval']);
								while ($aid_list = DB::fetch($aid_query)) {
									$aid_arr[] = $aid_list;
								}
								preg_match_all("/<img.*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i",str_ireplace("\\","",$aid_arr[0][content]),$aidarrs);
								$data ['pic_path'] = $aidarrs[1][0];
							}
						}else{
							$data ['pic_path'] = "";
						}
						$data ['pic_toUrl'] = $val['cid'];
						$picItemdata[]=$data;
					}
					unset($data);
					unset($val['imgval']);
				}
			} 
			//print_r($picItemdata);exit;

			
			/*-----module list------*/
			$itemData=array();
			if($moduleId==0){
				$dhList1=DB::query(" SELECT * FROM ".DB::table('add_module')." ORDER BY display DESC limit 1");
				while($dhList2 = DB::fetch($dhList1)) {
					$dhListarr[] = $dhList2;
				}
				$rx_moduleid=empty($dhListarr[0]['id'])?-1:$dhListarr[0]['id'];
			}else{
				$rx_moduleid=$moduleId;
			}
			
			$dhList_query = DB::query(" SELECT * FROM ".DB::table('add_module')." where id=".$rx_moduleid." limit 1");
			while($dhList_list=DB::fetch($dhList_query)){
				$dhList[]=$dhList_list;
			}
			$fenye_query=DB::fetch(DB::query(" SELECT count(*) as num FROM ".DB::table('add_portal_module')." where mid=".$rx_moduleid." AND isimage !=1"));
			//print_r($dhList);exit;
			$smile_query = DB::query("SELECT code FROM ".DB::table('common_smiley')." WHERE type = 'smiley'");
			while ($smile_list = DB::fetch($smile_query)) {
				$smile_arr[] = $smile_list;
			}
			foreach($smile_arr as $sr){
				$smiles[]=$sr[code];
			}
			if($dhList){
				if($dhList[0]['content']==2){
					$arr_query = DB::query(" SELECT * FROM ".DB::table('add_portal_module')." where mid=".$rx_moduleid."  AND isimage !=1  ORDER BY time DESC limit 0,1");
					while($arr_list=DB::fetch($arr_query)){
						$module_arr[]=$arr_list;
					}
				}else{
					$arr_query = DB::query(" SELECT * FROM ".DB::table('add_portal_module')." where mid=".$rx_moduleid."  AND isimage !=1  ORDER BY time DESC limit $start_limit,$limit");
					while($arr_list=DB::fetch($arr_query)){
						$module_arr[]=$arr_list;
					}
				}
				$pt1=Common::get_web_unicode_charset('\u6765\u81ea\u5b89\u5353\u5ba2\u6237\u7aef');
				$pt2=Common::get_web_unicode_charset('\u6765\u81ea\u0069\u0070\u0068\u006f\u006e\u0065\u5ba2\u6237\u7aef');
				
			
				foreach($module_arr as $key=>$val)
				{
					if($val['cidtype'] =='tid')
					{  
						$thread = get_thread_by_tid($val[cid]);
						$First_querya=DB::query("SELECT * FROM ".DB::table('forum_post')." WHERE tid=".(Int)$val['cid']." AND first=1 ");
						while ($First_rows = DB::fetch($First_querya)) {
							$First_arr[] = $First_rows;
						}	
						
						foreach($First_arr as $faa){
							$data ['board_id'] = ( int ) $thread ['fid'];
							$data ['topic_id'] = ( int ) $thread ['tid'];
							$data ['article_id'] = ( int ) 0;
							$data ['title'] = $thread['subject'];
							$data ['user_id'] = ( int ) $thread ['authorid'];
							$data ['last_reply_date'] = ($thread ['lastpost']) . "000";
							$data ['user_nick_name'] =Common::get_unicode_charset('\u533f\u540d');
							$data ['hits'] = ( int ) $thread ['views'];
			
							preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i",  $faa ['message'] ,$matches);
							$patten  = array("\r\n", "\n", "\r",$pt1,$pt2);
							$data ['subject'] =str_replace($matches[1], '', $faa ['message']);
							$data ['subject'] =str_replace($patten, '', $data ['subject']);
							$data ['subject'] = preg_replace("#(\w*)\[.*?\](\w*)#","$1$2",$data ['subject']);
							foreach($smiles as $si){
								$data ['subject'] =str_replace($si, '', $data ['subject']);
							}
							$data ['subject'] =trim($data ['subject']);
							$data ['subject'] = sub_str($data ['subject'], 0,40);
			
							$data ['replies'] = ( int ) $thread ['replies'];
							$topicInstance = new topic();
							//$pic = C::t('forum_thread') ->fetch_all_threadimage($thread);
							$query = DB::query ( "SELECT B.*,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $thread['tid']);
							$pic = DB::fetch($query);
							if(!empty($pic)){
								$data ['pic_path'] = $topicInstance->parseTargeImage($pic);
							}else{
								$data ['pic_path'] = "";
							}
						}
						//print_r($thread['tid']);
						//print_r($data);exit;
						$itemData[]=$data;
						$count=$fenye_query[num];
					}else if ($val['cidtype'] =='aid'){
						$portal_listt=DB::query("SELECT a.*,b.content,c.viewnum FROM ".DB::table('portal_article_title')." a,".DB::table('portal_article_content')." b,".DB::table('portal_article_count')." c WHERE a.aid=b.aid AND a.aid=c.aid AND a.aid=".(Int)$val['cid']."");
						while($portal_valuee = DB::fetch($portal_listt)) {
							$portals[] = $portal_valuee;
						}
						$data ['board_id'] = ( int )0;
						$data ['topic_id'] = ( int )0;
						$data ['article_id'] = ( int ) $val['cid'];
						$data ['title'] = $portals[0]['title'];
						$data ['user_id'] = ( int ) $portals[0]['uid'];
						$data ['last_reply_date'] = ($portals[0] ['dateline'])."000";
						$data ['user_nick_name'] = $portals[0]['username'];
						$data ['hits'] = ( int )$portals[0]['viewnum'];
						$data ['subject'] = sub_str($portals[0] ['summary'],0,40);
						$data ['replies'] = 0;
						$topicInstance = new topic();
						$portals_fromurl = trim($portals[0]['fromurl']);
						if($portals[0]['idtype']=="tid"){ //tie zi sheng cheng de wen zhang
							 $query = DB::query ( "SELECT B.*,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int )$portals[0]['id']);
							 $pic = DB::fetch($query);
							 if(!empty($pic)){
							 	$data ['pic_path'] = $topicInstance->parseTargeImage($pic);
							 }else{
							 	$data ['pic_path'] = "";
							 }
						}else{
							preg_match_all("/<img.*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i",str_ireplace("\\","",$portals[0]['content']),$arrss);
							if(!empty($arrss[1][0])){
								if(substr($arrss[1][0],0,7)!="http://"){
									$data ['pic_path'] = '/'.$arrss[1][0];
								}else{
									$data ['pic_path'] = $arrss[1][0];
								}
							}
						}
						$itemData[]=$data;
						$count=$fenye_query[num];
					}else if ($val['cidtype'] =='bid'){
						$picNew = new topic();
						switch ($val ['essence']) {
							case 1 :
								$digest = array (3,2,1);
								break;
							default:
								$digest = array (3,2,1,0);
						}
						$digest = implode(',', $digest);
						$threadlist_query=DB::query("SELECT a.*,b.message FROM ".DB::table('forum_thread')." a,".DB::table('forum_post')." b WHERE a.tid=b.tid AND a.displayorder!=-1 AND a.fid=".(Int)$val['cid']." AND b.subject!='' AND a.digest IN (".$digest.") ORDER BY a.dateline DESC limit $start_limit,$limit");
						while ($threadlist_list = DB::fetch($threadlist_query)) {
							$threadlist[] = $threadlist_list;
						}
						foreach ( $threadlist as $k => $group ) {
							$query = DB::query("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$group['tid']." AND p.first =1");
							while ($rows = DB::fetch($query)) {
								$ForumImg = $rows;
							}
							if ($ForumImg ["attachment"] == 2) {
								//$pic = C::t('forum_thread') ->fetch_all_threadimage($group);
								$query = DB::query ( "SELECT B.*,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $group['tid']);
								$pic = DB::fetch($query);
								if(!empty($pic)){
									$filename = $picNew->parseTargeImage($pic);
								}
							}
							$data ['board_id'] = ( int ) $group ['fid'];
							$data ['topic_id'] = ( int ) $group ['tid'];
							$data ['article_id'] = ( int )0;
							$data ['title'] = sub_str($group ['subject'], 0,40);
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
							preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i",  $group ['message'] ,$matches);
							$patten  = array("\r\n", "\n", "\r",$pt1,$pt2);
							$data ['subject'] =str_replace($matches[1], '', $group ['message']);
							$data ['subject'] =str_replace($patten, '', $data ['subject']);
							$data ['subject'] = preg_replace("#(\w*)\[.*?\](\w*)#","$1$2",$data ['subject']);
							foreach($smiles as $si){
								$data ['subject'] =str_replace($si, '', $data ['subject']);
							}
							$data ['subject'] =trim($data ['subject']);
							$data ['subject'] = sub_str($data ['subject'], 0,40);
							if ($filename) {
								$data ['pic_path'] = $filename;
							}else {
								$data ['pic_path'] = '';
							}
							unset ( $filename );
							$ret_pic_path = '';
							$itemData [] = $data;
						}
						//print_r($data);exit;
						$rxfen_query=DB::fetch(DB::query("SELECT count(*) as num FROM ".DB::table('forum_thread')." WHERE fid=".(Int)$val[cid]." AND digest IN (".$digest.")"));
						$count=$rxfen_query[num]; 
					}else if ($val['cidtype'] =='fid'){
						/*2013-7-18*/
						$artice_query=DB::query("SELECT * FROM ".DB::table('portal_article_title')."  WHERE catid=".(Int)$val['cid']." ORDER BY dateline DESC limit $start_limit,$limit");
						while ($artice_list = DB::fetch($artice_query)) {
							$artice[] = $artice_list;
						}
						foreach ( $artice as $k => $group ) {
							$artice_content=DB::fetch(DB::query("SELECT content FROM ".DB::table('portal_article_content')."  WHERE aid=".( int )$group['aid']));
							$artice_view=DB::fetch(DB::query("SELECT viewnum FROM ".DB::table('portal_article_count')."  WHERE aid=".( int )$group['aid']));
							$group['content']=$artice_content[content];
							$data ['board_id'] = ( int )0;
							$data ['topic_id'] = ( int )0;
							$data ['article_id'] = ( int )$group['aid'];
							$data ['title'] = sub_str($group ['title'], 0,40);
							$data ['user_id'] =$group['uid'];
							$data ['last_reply_date'] = ($group ['dateline']) . "000";
							$data ['user_nick_name'] = $group ['username'];
							$data ['hits'] = ( int )$artice_view[viewnum];
							$data ['replies'] = ( int ) 0;
							$data ['subject'] = $group ['summary'];
							
							$portals_fromurl = trim($group['fromurl']); 
							if($group['idtype']=="tid"){ //tie zi sheng cheng de wen zhang
								$query = DB::query ( "SELECT B.*,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int )$group['id']);
								$pic = DB::fetch($query);
								if(!empty($pic)){
									$topicInstance = new topic();
									$data ['pic_path'] = $topicInstance->parseTargeImage($pic);
								}else{
									$data ['pic_path'] = "";
								}
							}else{
								preg_match_all("/<img.*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i",str_ireplace("\\","",$group['content']),$arrss);
								if(!empty($arrss[1][0])){
									if(substr($arrss[1][0],0,7)!="http://"){
										$data ['pic_path'] = '/'.$arrss[1][0];
									}else{
										$data ['pic_path'] = $arrss[1][0];
									}
								}
							}
 							$ret_pic_path = '';
							$itemData [] = $data;
							unset($data ['pic_path']);
						}
						$rxye_query=DB::fetch(DB::query("SELECT count(*) as num FROM ".DB::table('portal_article_title')." WHERE catid=".(Int)$val['cid']));
						$count=$rxye_query[num];
					}
					unset($portals);
					unset($data);
					unset($val['imgurl']);
					unset($filename);
				}
			}
			/*end */
		} 
				
		$picItemdata = empty($picItemdata)?array():$picItemdata;
		$N = ceil ( $count / $limit );
		$thread_info = array (
				
				'has_next' => ($page>=$N || $N==1) ?0:1,
				"img_url" => DISCUZSERVERURL,
				"total_num" => $count,
				"page" => $page,
				"piclist" =>isset($moduleId)&&!empty($moduleId)|| $page>1?array():$picItemdata,
				'list' => empty($itemData)?array():$itemData,
				'rs' => 1
		);
		return $thread_info;
	}
	public function commonData($val)
	{
		if($val['idtype'] =='aid')
		{
			$article = C::t('portal_article_title')->fetch($val['id']);
			$tid = $article['id'];
			$article_id = $val[id];
		}else{
			$tid =$val['id'];
			$article_id = 0;
		}
		
		$topicInstance = new topic();
		$item = C::t ( 'forum_thread' )->fetch($tid);
		$ForumImg = DB::fetch_first("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$tid." AND p.first =1");
		
		if($tid==0){
			$filename = '/data/attachment/'.$article['pic'];
		}else{
			if ($ForumImg ["attachment"] == 2) {
				$parameter = array (
						'forum_threadimage',
						( int ) $item ['tid'],
				);
				$pic = DB::fetch_first ( "SELECT tid,attachment from %t where tid=%d", $parameter );
				if(!empty($pic)){
					$filename = $topicInstance->parseBigTargeImage($pic);
				}
			}
			if($item ["special"] ==2)
			{
				$query = C::t('forum_trade')->fetch_all_thread_goods($item['tid']);
				foreach($query as $trade) {
					$tradesaids[] = $trade['aid'];
					$tradespids[] = $trade['pid'];
				}
			
				$specialadd2 = 1;
				if($tradespids) {
					foreach(C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$item['tid'], 'pid', $tradespids) as $attach) {
						if($attach['isimage'] && is_array($tradesaids) && in_array($attach['aid'], $tradesaids)) {
							$trades[$attach['pid']]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
							$trades['thumb'] = $attach['thumb'] ? getimgthumbname($trades[$attach['pid']]['attachurl']) : $trades[$attach['pid']]['attachurl'];
							$trades[$attach['pid']]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
						}
					}
				}
				if(!empty($trades))
				{
						
					$trades['thumb'] = str_replace('forum/', '', $trades['thumb']);
					$filename = $topicInstance ->parseBigTradeTopicImg($trades);
				}
			}
		}
	
		$data ['board_id'] = ( int ) $item ['fid'];
		$data ['topic_id'] = ( int ) $item ['tid'];
		$data ['article_id'] = ( int ) $article_id;
		$data ['title'] = preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",sub_str($val ['title'], UC_DBCHARSET));
		$data ['user_id'] = ( int ) $item ['authorid'];
		$data ['last_reply_date'] = ($item ['lastpost']) . "000";
		if(empty($item ['author']))
		{
			$data ['user_nick_name'] =Common::get_unicode_charset('\u533f\u540d');
		}
		else
		{
			$data ['user_nick_name'] = $item ['author'];
		}
		$data ['hits'] = ( int ) $item ['views'];
		$data ['subject'] = preg_replace("#(\w*)\[.*?\](\w*)#","$1$2",$ForumImg ['message']);
		$data ['subject'] = sub_str($data ['subject'], 1,40);
		$data ['replies'] = ( int ) $item ['replies'];
		$data ['top'] = ( int ) $item ['displayorder'] > 0 || ( int ) $item ['icon'] ==13 || (int ) $item ['stamp'] ==4? 1 : 0;
		$data ['status'] = ( int ) $item ['status'];
		$data ['essence'] = ( int ) $item ['digest'] >0 || ( int ) $item ['icon'] ==9 || (int ) $item ['stamp'] ==0? 1 : 0;
		$data ['hot'] = ( int ) $item ['stamp'] ==1 || ( int ) $item ['icon'] ==10? 1 : 0;
		if ($filename) {
			$data ['pic_path'] = $filename;
		}else {
			$data ['pic_path'] = '';
		}
		return $data;
	}
	public function common_board_tid($val,$tid,$orig){
		$pt1=Common::get_web_unicode_charset('\u6765\u81ea\u5b89\u5353\u5ba2\u6237\u7aef');
		$pt2=Common::get_web_unicode_charset('\u6765\u81ea\u0069\u0070\u0068\u006f\u006e\u0065\u5ba2\u6237\u7aef');
		$thread = get_thread_by_tid($tid); 
		$orig ['message'] =preg_replace( "/\[attach\]\d+\[\/attach\]/i",'',$orig ['message']);
		$title =!empty($val['title'])?Common::get_unicode_charset($val['title']):$thread['subject'];
		$data ['board_id'] = ( int ) $thread ['fid'];
		$data ['topic_id'] = ( int ) $thread ['tid'];
		$data ['article_id'] = ( int ) 0;
		$data ['title'] = $title;
		$data ['user_id'] = ( int ) $thread ['authorid'];
		$data ['last_reply_date'] = ($thread ['lastpost']) . "000";
		$data ['user_nick_name'] =Common::get_unicode_charset('\u533f\u540d');
		$data ['hits'] = ( int ) $thread ['views'];
		$topicContent = discuzcode ( $orig ['message'],-1, $orig ['bbcodeoff'] );
		$topicContent = str_replace('<img src="static/', '<img src="/static/', $topicContent);
		$orig ['message'] = text_replace($topicContent);
		$orig ['message'] = $str = preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$topicContent); 
		$hide_data = preg_replace('/\[hide\S*\[\/hide\]/', '', $orig ['message'] );
		$data ['subject'] = text_replace(sub_str($hide_data,0,40));
		$patten  = array("\r\n", "\n", "\r",$pt1,$pt2);
		$data ['subject'] =str_replace($patten, '', $data ['subject']);
		$data ['subject'] =trim($data ['subject']);
		$data ['replies'] = ( int ) $thread ['replies'];
		$topicInstance = new topic();
		 
		if($val['isimage']==0){
			$ForumImg = DB::fetch_first("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$thread ['tid']." AND p.first =1");
			if ($val ["attachment"] == 2) {
				$parameter = array (
						'forum_threadimage',
						( int ) $val ['tid'],
				);
				$pic = DB::fetch_first ( "SELECT tid,attachment from %t where tid=%d", $parameter );
				if(!empty($pic)){
					$filename = $topicInstance->parseBigTargeImage($pic);
				}
			}
			$data ['pic_path'] = $filename;
		}else if (!empty($val['imgval']) && $val['imgtype'] == 'tid') {
			$val['imgval'] = is_numeric($val['imgval'])?$val['imgval']:-1;
			$ForumImg = DB::fetch_first("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$val['imgval']." AND p.first =1");
			if ($val ["attachment"] == 2) {
				$parameter = array (
						'forum_threadimage',
						( int ) $val ['tid'],
				);
				$pic = DB::fetch_first ( "SELECT tid,attachment from %t where tid=%d", $parameter );
				if(!empty($pic)){
					$filename = $topicInstance->parseBigTargeImage($pic);
				}
			}
			$data ['pic_path'] = $filename;
		}else {
			$data ['pic_path'] = $val['imgval'];
		}
		//print_r($data);exit;
		return $data;
		
	}
	
	public function common_artic_aid($val,$tid,$thread,$page){
		$setting_list = DB::query("SELECT * FROM ".DB::table('common_setting'));
		while($setting_value = DB::fetch($setting_list)) {
			$setting[] = $setting_value;
		}
		foreach($setting as $st){
			if($st[skey]=='ftp'){
				$myval=unserialize($st[svalue]);
				$ftp_host=$myval[host];
				$ftp_attachurl=$myval[attachurl];
			}
		}
		
		$tid = $val['cidtype']=='fid'?(int)$thread ['aid']:(int)$val['cid'];
		$content =table_portal_article_count::count_by_aid($tid);
		$title =!empty($val['title'])?Common::get_unicode_charset($val['title']):$thread['title'];
		$data ['board_id'] = ( int )0;
		$data ['topic_id'] = ( int )0;
		$data ['article_id'] = $val['cidtype']=='fid'?(int)$thread ['aid']:(int)$val['cid'];
		$data ['title'] = $title;
		$data ['user_id'] = ( int ) $thread ['uid'];
		$data ['last_reply_date'] = ($thread ['dateline']) . "000";
		$data ['user_nick_name'] = $thread ['username'];
		$data ['hits'] = ( int )$content['viewnum'];
		$data ['subject'] = $thread ['summary'];
		$data ['replies'] = ( int ) $content['commentnum'];
		$topicInstance = new topic();
		
		if(empty($ftp_host) && empty($ftp_attachurl)){
			if($val['isimage']==0){
				$data ['pic_path'] = empty($thread['pic'])?$val['imgval']:'/data/attachment/'.$thread['pic'];
			}else if (!empty($val['imgval']) && $val['imgtype']=='aid') {
				$data ['pic_path'] = empty($thread['pic'])?$val['imgval']:'/data/attachment/'.$thread['pic'];
			}else {
				$data ['pic_path'] = $val['imgval'];
			}
		}else{
			if($val['isimage']==0){
				$data ['pic_path'] = empty($thread['pic'])?$val['imgval']:$ftp_attachurl.$thread['pic'];
			}else if (!empty($val['imgval']) && $val['imgtype']=='aid') {
				$data ['pic_path'] = empty($thread['pic'])?$val['imgval']:$ftp_attachurl.$thread['pic'];
			}else {
				$data ['pic_path'] = $val['imgval'];
			}
		}
		return $data;
	}
}
