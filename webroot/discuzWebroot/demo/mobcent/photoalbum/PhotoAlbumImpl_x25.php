<?php
require_once './abstractPhotoAlbum.php';
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/class/table/table_forum_thread.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/constants.php';
require_once '../model/table_forum_thread.php';
require_once '../helper/helper_notification.php';
require_once '../model/table_surround_user.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'class/credit' );
require_once libfile ( 'function/post' );
require_once libfile ( 'function/forum' );
require_once '../model/table/x25/table_common_member.php';
require_once '../model/table/x25/topic.php';

class PhotoAlbumImpl_x25 extends abstractPhotoAlbum { 
	public function getPlugSignObj() { 
		function isdate($str,$format="Y-m-d"){
			$strArr = explode("-",$str);
			if(empty($strArr)){
				return false;
			}
			foreach($strArr as $val){
				if(strlen($val)<2){
					$val="0".$val;
				}
				$newArr[]=$val;
			}
			$str =implode("-",$newArr);
			$unixTime=strtotime($str);
			$checkDate= date($format,$unixTime);
			if($checkDate==$str){
				return true;
			}else{
				return false;
			}
		}
		
		$rPostion = $_GET['r'] ? $_GET['r']:0;  
		$longitude =$_GET['longitude'];	
		$latitude =	$_GET['latitude'];	
		$location	=	echo_urldecode($_GET['location']);
		$aid = $_REQUEST ['aid']; 
		$aid_Img=explode(',',$aid);
		$_G ['fid'] = $_GET ['boardId'];
		require_once '../public/mobcentDatabase.php';
		$info = new mobcentGetInfo ();
		$modnewposts = $info ->getBoard($_G ['fid']);
		$readperm = 0;
		$price = 0;
		$typeid = 0;
		$sortid = 0;
		$displayorder = $modnewposts['modnewposts'] > 0?-2:0;
		$digest = 0;
		$special = 0;
		$attachment = 0;
		$moderated = 0;
		$isgroup = 0;
		$replycredit = 0;
		$closed = 0;
		$publishdate = time ();
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
		//$uid = $arrAccess['user_id'];
		$uid = $_GET['userId']?(Int)$_GET['userId']:-1;
		$space = $info->getUserInfo ( intval ( $uid ) );
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		$author = $space ['username'];
		$_G ['username'] = $lastposter = $author;
		$_G = array_merge ( $_G, $space );
		 
	 	/*renxing user album...2013-09-04*/
		//echo $uid;
		$xm=new topic();
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20;
		$start_limit = ($page - 1) * $limit;
		
		$types=$_GET['type'];
		if($types=="list"){
			$list_count=DB::fetch(DB::query("SELECT count(*) as nums FROM ".DB::table('home_album')." where uid = ".$uid));
			$count=$list_count[nums];
			$list_query=DB::query("SELECT * FROM ".DB::table('home_album')." where uid = ".$uid." order by albumid desc limit $start_limit,$limit");
			while($list_list=DB::fetch($list_query)){
				$list_arr[]=$list_list;
			}
			//print_r($list_count);
			$data=array();
			foreach($list_arr as $list){
				$data ['aid'] = ( int ) $list['albumid'];
				$data ['user_id'] = ( int ) $uid;
				$data ['dateline'] = $list ['dateline']."000";
				$data ['updatetime'] = $list ['updatetime']."000";
				$data ['user_nick_name'] = $list ['username'];
				$data ['pic_path'] = '/data/attachment/album/' . $list['pic'];
				$data ['title'] = $list['albumname'];
				$data ['info'] = $list['depict'];
				$data_post[] = $data;
			}
			
		}elseif($types=="img"){
			$albumid=$_GET['aid'];
			$img_count=DB::fetch(DB::query("SELECT count(*) as nums FROM ".DB::table('home_pic')." where uid = ".$uid." and albumid = ".$albumid));
			$count=$img_count[nums];
			$img_query=DB::query("SELECT * FROM ".DB::table('home_pic')." where uid = ".$uid." and albumid = ".$albumid." order by picid desc limit $start_limit,$limit");
			while($img_list=DB::fetch($img_query)){
				$img_arr[]=$img_list;
			}
		 	
			$data=array();
			foreach($img_arr as $img){
				$comment_query=DB::fetch(DB::query("SELECT max(dateline) as last_reply_date FROM ".DB::table('home_comment')." where id = ".$img['picid']));
				$count_query=DB::fetch(DB::query("SELECT count(*) as count FROM ".DB::table('home_comment')." where id = ".$img['picid']));
				$data ['board_id'] = (Int)$albumid;
				$data ['topic_id'] = (Int)$img['picid'];
				$data ['title'] = $img['title']==""?$img['filename']:$img['title'];
				$data ['user_id'] = ( int ) $uid;
				$data ['create_time'] = $img[dateline]."000";
				$data ['last_reply_date'] = $comment_query[last_reply_date]."000";
				$data ['user_nick_name'] = $img ['username'];
				$data ['hits'] = ( int )$count_query[count];
				$data ['replies'] = ( int )$count_query[count];
				$data ['pic_path'] = '/data/attachment/album/' . $img['filepath'];
				$data_post[] = $data;
			}
		}
		
		if($count-($page * $limit) > 0){
			$has_next = (int)1;
		}else{
			$has_next = (int)0;
		}
		
		$thread_info = array (
				"img_url" => DISCUZSERVERURL,
				"total_num" => (Int)$count,
				"page" => (Int)$page,
				"has_next" => $has_next,
				'list' => empty($data_post)?array():$data_post,
				'rs' => 1
		);
		return $thread_info;	
	
	}
	
}

?>