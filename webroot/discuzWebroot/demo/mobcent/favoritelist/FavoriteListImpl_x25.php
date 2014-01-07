<?php
require_once './abstractFavoriteList.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/Thumbnail.php';
require_once '../public/mobcentDatabase.php';
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_forum_typeoptionvar.php';
require_once '../model/table/x25/table_forum_threadtype.php';
require_once '../model/table/x25/table_forum_threadclass.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
class FavoriteListImpl_x25 extends abstractFavoriteList {
	public function getFavoriteListObj() {
		$info = new mobcentGetInfo();
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
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		
		$page 		= $_GET['page'] ? $_GET['page']:1; 
		$limit 		= $_GET['pageSize']?$_GET['pageSize']:10; 
		$start 		= ($page-1) * $limit - 1;	 
		$idtype		= $_GET['type']?$_GET['type']:'tid';
		$parameter = array('home_favorite', 'forum_thread', $uid);
		$fav = DB::fetch_all("SELECT b.* FROM %t AS a INNER JOIN %t AS b ON a.id = b.tid WHERE a.uid = %d ORDER BY dateline DESC ".DB::limit($start, $limit),$parameter,'favid');
		foreach($fav as $pid_key => $pid)
		{
			$tids[$pid['tid']][] = $pid_key;
			if ($pid ["attachment"] == 2) {
				$tidimgs[$pid['tid']][] = $pid_key;
			}
		}
		
		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$picNew = new topic();
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
				if (array_key_exists($val,$tidimgs)) {
					$parameter = array (
							'forum_threadimage',
							( int ) $val
					);
					$pic = DB::fetch_first ( "SELECT tid,attachment from %t where tid=%d", $parameter );
					$pic_path = $picNew->parseTargeImage($pic);
				}
		
				if ($pic_path && $pic){
					$data ['pic_path'] = $pic_path;  
				}else {
					$data['pic_path'] = '';
				}
				unset($pic_path);
				$parameter2 = array (
						C::t ( 'forum_thread' )->get_table_name (),
						'home_favorite',
						$val
				);
				$val_subject = DB::fetch_first("SELECT a.tid,a.typeid,a.sortid,a.subject,a.author,a.lastpost,a.views,a.replies, b.description,b.spaceuid FROM %t AS a INNER JOIN %t AS b  ON a.tid = b.id WHERE tid=%d " . DB::limit ( 0, 1 ), $parameter2);
				$user_infor = getuserbyuid ( $val_subject['spaceuid'] );
				$data['topic_id'] = (int)$val_subject["tid"];
				$data ['type_id'] = (int)$val_subject["typeid"];
				$data ['sort_id'] = (int)$val_subject["sortid"];
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
			/*[fen lei ming cheng]*/
			for($i=0;$i<count($datas);$i++){
				$fenlei_name= C::t ( 'forum_threadtype' )->fetch_name_by_typeid ($datas[$i][sort_id]);
				foreach($fenlei_name as $fl){
					$fenleimingcheng=$fl['name'];
					$datas[$i][title]="[".$fenleimingcheng."]".$datas[$i][title];
				}
			}
			for($i=0;$i<count($datas);$i++){
				$fenlei_type= C::t ( 'forum_threadclass' )->fetch_all_by_typeid ($datas[$i][type_id]);
				foreach($fenlei_type as $ftype){
					$fenleitypemingcheng=$ftype['name'];
					$datas[$i][title]="[".$fenleitypemingcheng."]".$datas[$i][title];
				}
			}
			/*[end fen lei ming cheng]*/
		
		}
		$count = C::t('home_favorite')->count_by_uid_idtype($uid, $idtype);
		$N = ceil($count/$limit);
		$data_fav['list'] = $datas;
		$data_fav['total_num']= ( int ) $count;
		
		$data_fav['rs'] = (Int)1;
		$data_fav['page'] = (Int)$page;
		$data_fav['has_next'] = ($page>=$N || $N==1) ?0:1;  
		return $data_fav;
			}
		}

?>