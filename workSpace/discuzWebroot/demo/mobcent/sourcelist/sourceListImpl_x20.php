<?php
require_once './abstracSourceList.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../../source/function/function_forumlist.php';
require_once '../model/class_core.php';
require_once '../model/table/x20/mobcentDatabase.php';
require_once '../model/table/x20/table_forum_thread.php';
require_once '../model/table/x20/topic.php';
require_once '../tool/tool.php';
require_once '../tool/Thumbnail.php';
require_once '../Config/public.php';
require_once '../install/checkModule.php';
define('IN_MOBCENT',1);
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();

require_once libfile ( 'function/discuzcode' );

class sourceListImpl_x20 extends abstracSourceList {
	public function getSourceListImplObj() {
		$page = $_GET ['page'] ? $_GET ['page'] : 1;		
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20;
		$start_limit = ($page - 1) * $limit;
		$moduleId = isset($_GET ['moduleId'])?$_GET ['moduleId']:0;
		$xm=new topic();
		$s=file_exists('../manage/App.xml')?join("",file('../manage/App.xml')):array();
		$pic=file_exists('../manage_x20/AddPic.xml')?join("",file('../manage_x20/AddPic.xml')):array();
		$result =$xm->xml_to_array($s);
		$picResult = $xm->xml_to_array($pic);	
 
		if($result['version'][0][0][0]==2){ 
			if($moduleId==0){
				$Img_list=DB::query(" SELECT * FROM ".DB::table('add_portal_module')." where isimage=1 ORDER BY display desc limit 0,5");
				while($Img_value = DB::fetch($Img_list)) {
					$ImgArr[] = $Img_value;
				}
				foreach($ImgArr as $key=>$val)
				{
					if($val['cidtype'] =='tid')
					{
						$val[cid] = is_numeric($val[cid])?$val[cid]:-1;
						$thread = get_thread_by_tid($val[cid]);
						$First_query = DB::query("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$val[cid]." AND p.first =1");
						while ($rows = DB::fetch($First_query)) {
							$arr[] = $rows;
						}
						$data ['board_id'] = ( int ) $thread ['fid'];
						$data ['topic_id'] = ( int ) $thread ['tid'];
						$data ['article_id'] = ( int ) 0;
						$data ['title'] = Common::get_unicode_charset($val[title]==""?$thread['subject']:$val[title]);
						$data ['user_id'] = ( int ) $thread ['authorid'];
						$data ['last_reply_date'] = ($thread ['lastpost']) . "000";
						$data ['user_nick_name'] =Common::get_unicode_charset('\u533f\u540d');
						$data ['hits'] = ( int ) $thread ['views'];
						$data ['subject'] = sub_str($arr[0]['message'],0,40);
						$data ['replies'] = ( int ) $thread ['replies'];
						$topicInstance = new topic();
						if (!empty($val['imgval']) &&  $val['isimage']==1 ) {
							if($val['imgtype']==""){
								$data ['pic_path'] = $val['imgval'];
							}elseif($val['imgtype']=="tid"){
								$val['imgval'] = is_numeric($val['imgval'])?$val['imgval']:-1;
								$threadtid = get_thread_by_tid($val[imgval]);
								$pic = C::t('forum_thread') ->fetch_all_threadimage($threadtid);
								$data ['pic_path'] = $topicInstance->parseTradeTopicImg($pic);
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
					}else if ($val['cidtype'] =='aid'){
						$val[cid] = is_numeric($val[cid])?$val[cid]:-1;
						$portal_list = DB::query("SELECT * FROM ".DB::table('portal_article_title')." where aid=".$val[cid]);
						while($portal_value = DB::fetch($portal_list)) {
							$portal[] = $portal_value;
						}
						$data ['board_id'] = ( int )0;
						$data ['topic_id'] = ( int )0;
						$data ['article_id'] = ( int ) $val['cid'];
						$data ['title'] = Common::get_unicode_charset($val[title]==""?$portal[0]['title']:$val[title]);
						$data ['user_id'] = ( int ) $portal[0]['uid'];
						$data ['last_reply_date'] = ($portal[0] ['dateline'])."000";
						$data ['user_nick_name'] = $portal[0]['username'];
						$data ['hits'] = 0; 
						$data ['subject'] = sub_str($thread ['summary'],0,40);
						$data ['replies'] = 0; 
						$topicInstance = new topic();
						if (!empty($val['imgval']) &&  $val['isimage']==1 ) {
							if($val['imgtype']==""){
								$data ['pic_path'] = $val['imgval'];
							}elseif($val['imgtype']=="tid"){
								$val['imgval'] = is_numeric($val['imgval'])?$val['imgval']:-1;
								$threadtid = get_thread_by_tid($val[imgval]);
								$pic = C::t('forum_thread') ->fetch_all_threadimage($threadtid);
								$data ['pic_path'] = $topicInstance->parseTradeTopicImg($pic);
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
					}else if ($val['cidtype'] =='bid'){
						switch ($val ['essence']) {
							case 1 :
								$digest = array (
								3,
								2,
								1
								);
								break;
							default:
								$digest = array (
								3,
								2,
								1,
								0
								);
						}
						$digest = implode(',', $digest);
						$query = DB::query ( "SELECT * FROM ".DB::table('forum_thread')." t WHERE  t.displayorder >'-1'  AND digest in(".$digest.")  AND t.fid = ".$val['cid']." ORDER BY t.tid desc" . DB::limit ( $start_limit, $limit ), 'tid' );
						while($rows = DB::fetch($query))
						{
							$threadlist_top[]=$rows;
						}
						$Tquery = DB::query ( "SELECT count(*) as num FROM ".DB::table('forum_thread')." t WHERE  t.displayorder >'-1'  AND digest in(".$digest.")  AND t.fid = ".$val['cid']." ORDER BY t.tid desc" . DB::limit ( $start_limit, $limit ), 'tid' );
						while($Trows = DB::fetch($Tquery))
						{
							$Tcount[]=$Trows;
						}
						$count['num']=$Tcount[0]['num'];
							
						foreach($threadlist_top as $key=>$thread)
						{
							$data ['board_id'] = ( int ) $thread ['fid'];
							$data ['topic_id'] = ( int ) $thread ['tid'];
							$data ['article_id'] = ( int ) 0;
							$data ['title'] = Common::get_unicode_charset($val[title]==""?$thread['subject']:$val[title]);
							$data ['user_id'] = ( int ) $thread ['authorid'];
							$data ['last_reply_date'] = ($thread ['lastpost']) . "000";
							$data ['user_nick_name'] =Common::get_unicode_charset('\u533f\u540d');
							$data ['hits'] = ( int ) $thread ['views'];
							$data ['subject'] = sub_str($thread ['message'],0,40);
							$data ['replies'] = ( int ) $thread ['replies'];
							$topicInstance = new topic();
							if (!empty($val['imgval']) && $val['imgtype'] == 'tid' && $val['isimage']==1) {
								$pic = C::t('forum_thread') ->fetch_all_threadimage($thread);
								if(!empty($pic)){
									$data ['pic_path'] = $topicInstance->parseTradeTopicImg($pic);
								}else{
									$data ['pic_path'] = $val['imgval'];
								}
							}else{
								$data ['pic_path'] = $val['imgval'];
							}
							$picItemdata[]=$data;
						}
							
					}else if ($val['cidtype'] =='fid'){
						$query = DB::query("SELECT * FROM ".DB::table('portal_article_title')."  WHERE catid=".$val[cid]);
						$count = DB::query("SELECT count(*) as num FROM ".DB::table('portal_article_title')."  WHERE catid=".$val[cid]);
						while($arr = DB::fetch($query)){
							$thread[] =$arr;
						}
						foreach($thread as $key=>$portal)
						{
							$data ['board_id'] = ( int )0;
							$data ['topic_id'] = ( int )0;
							$data ['article_id'] = ( int ) $val['cid'];
							$data ['title'] = Common::get_unicode_charset($val[title]==""?$portal['title']:$val[title]);
							$data ['user_id'] = ( int ) $portal['uid'];
							$data ['last_reply_date'] = ($portal['dateline'])."000";
							$data ['user_nick_name'] = $portal['username'];
							$data ['hits'] = 0; 
							$data ['subject'] = sub_str($thread ['summary'],0,40);
							$data ['replies'] = 0; 
							$topicInstance = new topic();
							if (empty($val['imgval'])) {
								$data ['pic_path'] = '/data/attachment/'.$thread['pic'];
							}else {
								$data ['pic_path'] = $val['imgval'];
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
								$pic = C::t('forum_thread') ->fetch_all_threadimage($threadtid);
								$data ['pic_path'] = $topicInstance->parseTradeTopicImg($pic);
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
	
			$smile_query = DB::query("SELECT code FROM ".DB::table('common_smiley')." WHERE type = 'smiley'");
			while ($smile_list = DB::fetch($smile_query)) {
				$smile_arr[] = $smile_list;
			}
			foreach($smile_arr as $sr){
				$smiles[]=$sr[code];
			}
	if($dhList){		
			if($dhList[0]['content']==2)
			{
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
						$pic = C::t('forum_thread') ->fetch_all_threadimage($thread);
						if(!empty($pic)){
							$data ['pic_path'] = $topicInstance->parseTradeTopicImg($pic);
						}else{
							$data ['pic_path'] = "";
						}
					}
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
					preg_match_all("/<img.*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i",str_ireplace("\\","",$portals[0]['content']),$arrss); 
					$data ['pic_path'] = $arrss[1][0];
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
					$threadlist_query=DB::query("SELECT a.*,b.message FROM ".DB::table('forum_thread')." a,".DB::table('forum_post')." b WHERE a.tid=b.tid AND a.fid=".(Int)$val['cid']." AND b.subject!='' AND a.digest IN (".$digest.") ORDER BY a.lastpost DESC limit $start_limit,$limit");
					while ($threadlist_list = DB::fetch($threadlist_query)) {
						$threadlist[] = $threadlist_list;
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
					$rxfen_query=DB::fetch(DB::query("SELECT count(*) as num FROM ".DB::table('forum_thread')." WHERE fid=".(Int)$val[cid]." AND digest IN (".$digest.")"));
					$count=$rxfen_query[num];
				}else if ($val['cidtype'] =='fid'){
					/*2013-7-18*/
					$artice_query=DB::query("SELECT a.*,b.content,c.viewnum FROM ".DB::table('portal_article_title')." a,".DB::table('portal_article_content')." b,".DB::table('portal_article_count')." c WHERE a.aid=b.aid AND a.aid=c.aid AND a.catid=".(Int)$val['cid']." ORDER BY a.dateline DESC ");
					while ($artice_list = DB::fetch($artice_query)) {
						$artice[] = $artice_list;
					}
					foreach ( $artice as $k => $group ) {
						$data ['board_id'] = ( int )0;
						$data ['topic_id'] = ( int )0;
						$data ['article_id'] = ( int )$group['aid'];
						$data ['title'] = sub_str($group ['title'], 0,40);
						$data ['user_id'] =$group['uid'];
						$data ['last_reply_date'] = ($group ['dateline']) . "000";
						$data ['user_nick_name'] = $group ['username'];
						$data ['hits'] = ( int )$group['viewnum'];
						$data ['replies'] = ( int ) 0;
						$data ['subject'] = $group ['summary'];
						preg_match_all("/<img.*src\s*=\s*[\"|\']?\s*([^>\"\'\s]*)/i",str_ireplace("\\","",$group['content']),$arrss); 
						$data ['pic_path'] = $arrss[1][0];
						$ret_pic_path = '';
						$itemData [] = $data;
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
		$N = ceil ( ($count) / $limit );
		$thread_info = array (		
				'has_next' => ($page>=$N || $N==1) ?0:1,
				"img_url" => DISCUZSERVERURL,
				"total_num" => $count,
				"page" => $page,
				"piclist" => $picItemdata,
				'list' => $itemData,
				'rs' => 1
			);
		
		
		return $thread_info;
		}
		
		public function commonData($val)
		{
			$picNew = new topic();
			$itemquery = DB::query("SELECT * FROM ".DB::table('forum_thread')." p WHERE tid=".$val['id']);
			while ($Itemrows = DB::fetch($itemquery)) {
				$item = $Itemrows;
			}
		
			$query = DB::query("SELECT * FROM ".DB::table('forum_post')." p WHERE tid=".$item['tid']." AND p.first =1");
			while ($rows = DB::fetch($query)) {
				$ForumImg = $rows;
			}
			if ($item ["attachment"] == 2) {
				if(!empty($val['pic'])){
					$img =substr($val['pic'],0,5);

					$pic=str_replace('forum','',$val['pic']);
					$filename =$picNew->parseTargeBigImage($pic);
				}
			}
			if($item ["special"] ==2)
			{
				$query = DB::query("SELECT * FROM ".DB::table('forum_trade')." WHERE tid='".$item['tid']."'  ORDER BY displayorder");
				while($trade = DB::fetch($query)) {
					$tradesaids[] = $trade['aid'];
					$tradespids[] = $trade['pid'];
				}
					
				$specialadd2 = 1;
				$tradespids = dimplode($tradespids);
				if($tradespids) {
					$query = DB::query("SELECT * FROM ".DB::table(getattachtablebytid($item['id']))." WHERE pid IN ($tradespids)");
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
			$data ['board_id'] = ( int ) $item ['fid'];
			$data ['topic_id'] = ( int ) $item ['tid'];
			
			$data ['title'] = sub_str($item ['subject'], 0,40);
			$data ['user_id'] = ( int ) $item ['authorid'];
			$data ['last_reply_date'] = ($item ['lastpost']) . "000";
			if(empty($val ['author']))
			{
				$data ['user_nick_name'] =Common::get_unicode_charset('\u533f\u540d');;
			}
			else
			{
				$data ['user_nick_name'] = $item ['author'];
			}
			$data ['hits'] = ( int ) $item ['views'];
			$data ['replies'] = ( int ) $item ['replies'];
			$data ['status'] = ( int ) $item ['status'];
			$data ['essence'] = ( int ) $item ['digest'] >0|| ( int ) $item ['icon'] ==9 || (int ) $item ['stamp'] ==0? 1 : 0;
			$data ['top'] = ( int ) $item ['displayorder'] >0|| ( int ) $item ['icon'] ==13 || (int ) $item ['stamp'] ==4? 1 : 0;
			 
			 
			$data ['hot'] = ( int ) $item ['stamp'] ==1|| ( int ) $item ['icon'] ==10? 1 : 0;
			if ($filename) {
				$data ['pic_path'] = $filename;
			}else {
				$data ['pic_path'] = '';
			}

			return $data;
	
		}


}
?>