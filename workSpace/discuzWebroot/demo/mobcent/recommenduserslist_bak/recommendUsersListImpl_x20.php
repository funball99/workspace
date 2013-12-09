<?php
define('UC_API',true);
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../uc_client/client.php';
require_once '../public/common_json.php';
require_once '../../config/config_ucenter.php';
require_once libfile ( 'function/friend' );
require_once ('./abstractRecommendUsersList.php');
require_once '../model/table/x20/table_home_friend.php';
require_once '../model/table/x20/mobcentDatabase.php';
class recommendUsersListImpl_x20 extends abstractRecommendUsersList {
	function getRecommendUsers() {
		try {
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
			$query = DB::query("SELECT t1.uid,t2.* FROM ".DB::table('home_specialuser')." as t1 INNER JOIN ".DB::table('common_member')." as t2 on t1.uid = t2.uid ORDER BY t2.credits DESC limit ".$start.','. $limit);
			while ($value = DB::fetch($query)) {
				$list[$value['uid']] = $value;
			}
			
			$arr = DB::fetch_first("SELECT count(*) as num FROM ".DB::table('home_specialuser')." limit ".$start.','. $limit);
			$count = $arr['num'];
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
				$blacklist .= ',' . $key;
			}
			$accessSecret = $_GET['accessSecret'];
			$accessToken = $_GET['accessToken'];
			$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
			$_G['uid'] = $uid = $arrAccess['user_id'];
			if(empty($uid))
			{
				return C::t('common_member') -> userAccessError();
				exit();
			}
			if ($uid && $followuids) {  
				$followuids = implode(',', $followuids);
				$friend_query = DB::query("SELECT * FROM ".DB::table('home_friend')." WHERE uid='$uid' AND fuid in(".$followuids.")");
				while ($friend_value = DB::fetch($friend_query)) {
					$followes[$friend_value['fuid']] = $friend_value;
				}
			}
			if (C::t ( 'home_blacklist' )->count_by_uid_buid ( $uid )) {
				$black_query = DB::query ( 'SELECT * FROM '.DB::table('home_blacklist').' WHERE buid='.$uid.' and uid in('.$blacklist.')');
				while ( $black_value = DB::fetch ( $black_query ) ) {
					$data_black [$black_value ['buid']] = $black_value;
				}
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
				$data_xx ['status'] = ( int )$list[$value['uid']]['status'] ;  
				$data_rc ['list'] [$value ['uid']] = $data_xx;
			}
			$info = C::t('common_member_profile') -> get_profile_by_uid ( $uids, 'gender' );
		
			$num = ceil ( $count / $limit );
			foreach ( $list as $k => $v ) {
				foreach($data_rc as $key =>$val)
				{
					if(array_key_exists($v ['uid'], $val))
					{
						$data_rc ['list'] [$v ['uid']] ['gender'] = $info[$v ['uid']]['gender'];
						$data_rc ['list'] [$v['uid']]['level'] =(int) $v['stars'];
						$data2 [] = $data_rc ['list'] [$v ['uid']];
					}
				}
					
			}
			$data_notice ['list'] = $data2;
			$data_notice ['icon_url'] = '';  
			$data_notice ['page'] = (Int)$page;
			$data_notice ['has_next'] = ($page >= $num || $num == 1) ? 0 : 1; 
			$data_notice ['rs'] = 1;
			
			return $data_notice;
		}catch(Exception $e)
		{
			$data_notice ['rs'] = 0;
			$data_notice ['error'] = '9999';
		}
		return  $data_notice;
		exit();
		}

}

?>