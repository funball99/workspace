<?php
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../model/table_common_member_profile.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile ( 'function/friend' );
require_once ('./abstractRecommendUsersList.php');
require_once ('../../source/class/table/table_home_specialuser.php');

class recommendUsersListImpl_x25 extends abstractRecommendUsersList {
	private $_table = 'common_member';
	private $recommond_table = 'home_specialuser';
	function getRecommendUsers() {
		try{
			$info = new mobcentGetInfo();
			$accessSecret = $_GET['accessSecret'];
			$accessToken = $_GET['accessToken'];
			$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
			$uid = (Int)$arrAccess['user_id'];
			
			$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
			$group = $info->rank_check_allow($accessSecret,$accessToken,$qquser);
			if(!$group['allowvisit'])
			{
				$data_post['rs'] = 0;
				$data_post['errcode'] = '01110001';
				return $data_post;
				exit();
			}
			$page = $_GET ['page'] ? $_GET ['page'] : 1;
			$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 10; 
			$start = ($page - 1) * $limit;  
			$end = $start + $limit;  
			$status = $_GET ['status'] = 1;
			$list_query=DB::query("select a.*,b.* from ".DB::table("common_member")." a, ".DB::table("common_member_profile")." b where a.uid=b.uid AND a.uid!=".$uid." ORDER BY a.credits desc limit $start,$limit");
			while($list_list=DB::fetch($list_query)){
				$list[]=$list_list;
			}
			$list_count=DB::fetch(DB::query("select count(*) as nums from ".DB::table("common_member")." WHERE uid != ".$uid));
			$count=(Int)$list_count[nums];
		 	//print_r($list);exit;
 
			$N = ceil ( $count / $limit );
			$data_list ['total_num'] = (Int)$count;
			$data_list ['icon_url'] = DISCUZSERVERURL;
			$data_list ['page'] = (Int)$page;
			$data_list ['has_next'] = ($page >= $N || $N == 1) ? 0 : 1;
			$data_list ['rs'] = 1;
			foreach ( $list as $k => $v ) {
				$friend_query=DB::fetch(DB::query("select count(*) as nums from ".DB::table("home_follow")." where uid=".$uid." AND followuid=".(Int)$v['uid']));
				$data['is_friend'] = $friend_query[nums]==0?0:1;
				$data['is_black'] = 0;
				$data['uid'] = $v['uid'];
				$data['name'] = $v['username'];
				$data['icon'] = userIconImg ($v['uid'] );  
				$data['status'] = $v['status'];
				$data['gender'] = $v['gender'];
				$data['level'] = 0;
				$data['credits'] = $v['credits'];
				$datas[]=$data;
			} 
			$data_list ['list'] = empty($datas)?array():$datas;
			return $data_list;
		}catch(Exception $e){
			$obj -> rs = 0;
			$obj -> errorcode = 99999999;
			file_put_contents("./error.log", $e->getMessage());
			return $obj;
		}
	}

}

?>