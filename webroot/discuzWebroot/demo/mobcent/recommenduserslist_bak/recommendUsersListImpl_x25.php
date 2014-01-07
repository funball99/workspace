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
			//$list = C::t ( 'home_specialuser' )->fetch_all_by_status ( $status, $start, $limit );
			$list_query=DB::query("select a.*,b.credits from ".DB::table("home_specialuser")." a, ".DB::table("common_member")." b where a.uid=b.uid ORDER BY b.credits desc");
			while($list_list=DB::fetch($list_query)){
				$list[]=$list_list;
			}
			//print_r($list);exit;
			$count = C::t ( 'home_specialuser' )->count_by_status ( $status, $start, $limit );
			$i = 0;
			$blacklist = 0;
			if($count == 0){
				$data_rc ['rs'] = 1;
				$data_rc ['icon_url'] = '';
				$data_rc ['list'] = array();
				$data_rc ['has_next'] = 0;
				return $data_rc;
				exit();

			}
			foreach ( $list as $key => $v ) {
				$followuids [] = $key;
			}
			$info = new mobcentGetInfo();
			$accessSecret = $_GET['accessSecret'];
			$accessToken = $_GET['accessToken'];
			$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
			$uid = $_G ['uid'] =$arrAccess['user_id'];
			if(empty($_G ['uid']))
			{
				return $info -> userAccessError();
				exit();
			}
			if ($uid) {
				$followes = C::t ( 'home_follow' )->fetch_all_by_uid_followuid ( $uid, $followuids );
			}
				$query = DB::query ( 'SELECT * FROM %t WHERE buid=%d', array (
						'home_blacklist',
						$uid
						
				) );
				while ( $value = DB::fetch ( $query ) ) {
					$data_black [$value ['uid']][] = $value;
				}
				
				
			foreach ( $list as $key => $value ) {
				$data_xx ['is_friend'] = 0;
				$data_xx ['is_black'] = 0;
				if (count ( $followes )) {
					foreach ( $followes as $k => $val ) {
						if ($key == $k) {
							$data_xx ['is_friend'] = 1;  
							unset ( $followes [$key] );
						}
					}
				}
				if (count ( $data_black )) {
					foreach ( $data_black as $k => $val ) {
						if ($key == $k) {
							$data_xx ['is_black'] = 1;
							unset ( $data_black [$key] );
						}
					}
				}
				$uids [] = $value ['uid'];
				$data_xx ['uid'] = ( int ) $value ['uid']; 
				$data_xx ['name'] = $value ['username']; 
				$data_xx ['icon'] = userIconImg ( $value ['uid'] );
				$data_xx ['status'] = ( int ) 1 - $value ['status']; 
				$data_rc ['list'] [$value ['uid']] = $data_xx;
			}
			$member_status_data = C::t ( 'common_session' )->fetch_all_by_uid ( $uids, $start, $limit );
			foreach ( $member_status_data as $k => $v ) {
				$member_status [$v ['uid']] = ( int ) 1 - $v ['invisible'];
			}
			$profile = new memberProfile ();
			$list = $profile->get_profile_by_uid ( $uids, 'gender' );
			
			foreach ( $list as $k => $v ) {
				$data_rc ['list'] [$v ['uid']] ['gender'] = $v ['gender'];
				$data_rc ['list'] [$v ['uid']] ['status'] = $member_status [$v ['uid']];
				$data_rc ['list'] [$v['uid']]['level'] =(int) $v['stars'];
				$data_rc ['list'] [] = $data_rc ['list'] [$v ['uid']];
				$data2 [] = $data_rc ['list'] [$v ['uid']];
			}
			if(empty($data2))
				$data_rc ['list'] =array();
			else 
			$data_rc ['list'] = $data2;
			$N = ceil ( $count / $limit );
			$data_rc ['icon_url'] = DISCUZSERVERURL;  
			$data_rc ['page'] = (Int)$page;
			$data_rc ['has_next'] = ($page >= $N || $N == 1) ? 0 : 1;  
			$data_rc ['rs'] = 1;
			return $data_rc;
		}catch(Exception $e){
			$obj -> rs = 0;
			$obj -> errorcode = 99999999;
			file_put_contents("./error.log", $e->getMessage());
			return $obj;
		}
	}

}

?>