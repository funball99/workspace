<?php
require_once './abstractFriends.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../Config/public.php';
require_once '../tool/tool.php';
require_once '../model/table_common_member_profile.php';
require_once '../public/mobcentDatabase.php';
define('IN_MOBCENT',1);
define('ALLOWGUEST', 1);
C::app ()->init ();

class postFriendsImpl_x25 extends abstractFriends {
	public function getFriendsObj() {
		$info = new mobcentGetInfo();
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 10; 
		$start = ($page - 1) * $limit;  
		$end = $start + $limit;  
		$data_friends = array ();
		
		$query = DB::query( "SELECT uid,username FROM %t WHERE groupid = 1", array (
				"common_member",
		) );
		while($value = DB::fetch($query)) {
			$data ['uid'] = ( int ) $value ['uid'];
			$data ['name'] = $value ['username'];
			$data ['gender'] = 1;
			$data ['role_num'] = 8;
			
			$data_friends ['list'] [] = $data;
		}
		$followes = C::t ( 'home_follow' )->fetch_all_following_by_uid ( $uid, '');
		$count = C::t ( 'home_follow' )->count_follow_user ( $uid );
		
		foreach ( $followes as $k => $followe ) {
			$group = $info-> sel_group_by_uid($followe ['followuid']);
			if($group['groupid'] == 1)
			{
				continue;
			}
			$data ['uid'] = ( int ) $followe ['followuid'];
			$data ['name'] = $followe ['fusername'];
			$data ['gender'] = (Int)1;
			$data ['role_num'] = (Int)2;
			
			$data_friends ['list'] [] = $data;
		}
		
		$data_friends ['rs'] = (Int)1;
		return $data_friends;
			}
		}

?>