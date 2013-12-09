<?php
require_once './abstractFavoriteList.php';
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../public/common_json.php';
require_once '../Config/public.php';
require_once '../tool/Thumbnail.php';
require_once '../model/table/x20/topic.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../uc_client/client.php';
require_once '../model/table/x20/mobcentDatabase.php';

class FavoriteListImpl_x20 extends abstractFavoriteList {
	public function getFavoriteListObj() {
		$info = new mobcentGetInfo();
		$topicInstance = new topic();
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
		$_G['uid'] = $uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		
		$page 		= $_GET['page'] ? $_GET['page']:1; 
		$limit 		= $_GET['pageSize']?$_GET['pageSize']:10; 
		
		$idtype		= $_GET['type']?$_GET['type']:'tid';
		if($page == 1)
		{
			$start = 0;
		}
		else
		{
			$start 		= ($page-1) * $limit - 1;	 
		}
		$parameter = array('home_favorite', 'forum_thread', $uid);
		$fav = DB::query("SELECT a.favid,b.* FROM ".DB::table('home_favorite')." AS a INNER JOIN ".DB::table('forum_thread')." AS b ON a.id = b.tid WHERE a.uid = $uid ORDER BY a.favid DESC limit $start, $limit");
		while($list = DB::fetch($fav))
		{
			$arr[$list['favid']] = $list;
		}
		foreach($arr as $pid_key => $pid)
		{
			$tids[$pid['tid']][] = $pid_key;
			if ($pid ["attachment"] == 2) {
				$tidimgs[$pid['tid']][] = $pid_key;
			}
		}
		
		
		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$smile_query = DB::query("SELECT code FROM ".DB::table('common_smiley')." WHERE type = 'smiley'");
		while ($smile_list = DB::fetch($smile_query)) {
			$smile_arr[] = $smile_list;
		}
		foreach($smile_arr as $sr){
			$smiles[]=$sr[code];
		}
		if(!empty($tids))
		{
			foreach ( array_keys($tids) as $k => $val ) {
				$arr['tid'] =$val;
				if (array_key_exists($val,$tidimgs)) {
					$pic = C::t('forum_thread')->fetch_all_threadimage($arr);
					$pic_path = $topicInstance->parseTargeImage($pic);
				}
		
				if ($pic_path && $pic){
					$data ['pic_path'] = $pic_path;  
				}else {
					$data['pic_path'] = '';
				}
				unset($pic_path);
				$val_subject = DB::fetch_first("SELECT a.tid,a.subject,a.author,a.lastpost,a.views,a.replies, b.description,b.spaceuid FROM ".DB::table('forum_thread')." AS a INNER JOIN ".DB::table('home_favorite')." AS b  ON a.tid = b.id WHERE tid=$val limit 0,1");
				$user_infor = getuserbyuid ($val_subject['spaceuid']);
				$data['topic_id']	=(int)$val_subject["tid"];
				$data ['title'] = sub_str($val_subject ['subject'],0,40);
				/*rx newadded 20130928*/
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
				$data['subject'] = sub_str($data_subject, 0,40);
				/*end rx 20130928*/
				$data['lastpost']		=$val_subject["lastpost"].'000';
				$data['views']		=$val_subject["views"];
				$data['replies']		=$val_subject["replies"];
				$data['user_nick_name']	=$user_infor['username'] .'('.$val_subject["description"].')'; 
				$datas[]=$data;
			}
		
		}
		$count = DB::fetch_first("SELECT COUNT(favid) AS num FROM ".DB::table('home_favorite')." WHERE  uid=".$uid. " AND idtype='tid'");
		$N = ceil($count['num']/$limit);
		$data_fav['list'] = $datas;
		$data_fav['rs'] = (Int)1;
		$data_fav['page'] = (Int)$page;
		$data_fav['has_next'] = ($page>=$N || $N==1) ?0:1;  
		return  $data_fav;
		}

}

?>