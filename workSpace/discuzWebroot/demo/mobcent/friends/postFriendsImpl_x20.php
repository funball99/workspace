<?php
require_once './abstractFriends.php';
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../Config/public.php';
require_once '../tool/tool.php';
define('IN_MOBCENT',1);
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../uc_client/client.php';


class postFriendsImpl_x20 extends abstractFriends {
	public function getFriendsObj() {
		try{
			$accessSecret = $_GET['accessSecret'];
			$accessToken = $_GET['accessToken'];
			$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
			$uid = $arrAccess['user_id'];
			if(empty($uid))
			{
				return C::t('common_member') -> userAccessError();
				exit();
			}
			$page = $_GET ['page'] ? $_GET ['page'] : 1;
			$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 10;  
			$start = ($page - 1) * $limit;  
			$end = $start + $limit; 
			$data_friends = array ();
			$query = DB::query( "SELECT uid,username FROM ".DB::table('common_member')." WHERE groupid = 1");
			while($value = DB::fetch($query)) {
				$data ['uid'] = ( int ) $value ['uid'];
				$data ['name'] = $value ['username'];
				$data ['gender'] = 1;
				$data ['role_num'] = 8;
			
				$data_friends ['list'] [] = $data;
			}
			$followes = C::t('home_follow')->fetch_all_following_by_uid($uid, $start, $limit);
			$count= C::t('home_follow')->count_follow_user($uid);
			foreach ( $followes as $k => $followe ) {
				$group = C::t ( 'common_member' )->getUsergroup ($followe ['fuid']);
				if($group['groupid'] == 1)
				{
					continue;
				}
				$data ['uid'] = ( int ) $followe ['fuid'];
				$data ['name'] = $followe ['fusername'];
				$data ['gender'] = 1;
				$data ['role_num'] = 2;
				$data_friends ['list'] [] = $data;
			}
			
			$data_friends ['rs'] = (Int)1;
			return $data_friends;
		}catch(Exception $e){
			$data_friends ['rs'] = (Int)0;
			$data_friends ['list'] [] =array();
			return $data_friends;
		}
		
		}

}

?>