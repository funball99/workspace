<?php
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../model/table_common_member_profile.php';
require_once '../model/table_home_follow.php';
require_once '../tool/constants.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once ('./abstractFollowUserList.php');
class followUserListImpl_x25 extends abstractFollowUserList {
	function getFollowUserList() {
		try{
			$isblack = 0;
			$obj = new table_home_follow ();
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
			$followuid = $_GET['userId'] ? $_GET['userId'] : $arrAccess['user_id'];
			if(empty($followuid))
			{
				return $info -> userAccessError();
				exit();
			}
			$page = $_GET ['page'] ? $_GET ['page'] : 1;
			$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 10;  
			$start = ($page - 1) * $limit; 
			$end = $start + $limit;  
			$followes = $obj->fetch_all_by_followuid ( $followuid, '', $start, $limit );
			$data_friends = array ();
			$count = $obj->count_follow_user ( $followuid, 1 );
			
			foreach ( $followes as $k => $followe ) {
			
				$uids [] = $followe ["uid"];
				$data_friends ['uid'] = $followe ["followuid"];
				$data_friends ['username'] = $followe ["fusername"];
				$data_friends ['user_icon'] = 'uc_server/avatar.php?uid=' . $followuid . '&size=small';
				$data ['uid'] = ( int ) $followe ['uid'];
				$data ['name'] = $followe ['username'];
				$data ['status'] = ( int ) $followe ["status"];
				$data ['icon'] = userIconImg ( $followe ['uid'] );
				$data_friends ['list'] [$followe ['uid']] = $data;
			}
			if(!empty($uids)){
				$member_status_data = C::t ( 'common_session' )->fetch_all_by_uid ( $uids, $start, $limit );
				foreach ( $member_status_data as $k => $v ) {
					$member_status [$v ['uid']] = empty($v['invisible'])?1:0;
				}
				$profile = new memberProfile ();
				$list = $profile->get_profile_by_uid ( $uids, 'gender',$start,$limit);
				$total =0;
				foreach ( $list as $k => $v ) {
						$query = DB::query( 'SELECT count(*) as num FROM %t WHERE buid=%d and uid =%d', array (
								'home_blacklist',
								$followuid,
								$v ['uid']
								
						) );
					while($value = DB::fetch($query)) {
					if($value['num'])
					{
						$isblack = 1;
					}
					
					$follow_num = C::t('home_follow')->is_friend($followuid,$v ['uid']);
					//$follow_num = DB::fetch_first ( "SELECT count(*) as count FROM ".DB::table('home_follow')." WHERE uid=".$followuid.' AND followuid = '.$v ['uid']);
					$data_friends ['list'] [$v ['uid']] ['status'] = $member_status [$v ['uid']];
					$data_friends ['list'] [$v ['uid']] ['gender'] = $v ['gender'];
					$data_friends ['list'] [$v['uid']]['level'] =(int) $v['stars'];
					$data_friends ['list'] [] = $data_friends ['list'] [$v ['uid']];
					$data_friends ['list'] [$v ['uid']]['is_friend'] = $follow_num ['count'] > 0?( int ) 1:( int ) 0;  
					$data_friends ['list'] [$v ['uid']]['is_black'] = $isblack;  
					$data2 [] = $data_friends ['list'] [$v ['uid']];
					$isblack = 0;
					$total =$total+1;
					}
				}
				$data_friends ['icon_url'] = DISCUZSERVERURL;  
				$data_friends ["img_url"] = $url;
				$data_friends ['list'] = $data2;
				$N = ceil ( $count / $limit );
				$data_friends ['page'] = (Int)$page;
				$data_friends ['count'] = (Int)$count;
				$data_friends ['has_next'] = ($page >= $N || $N == 1) ? 0 : 1;  
				$data_friends['rs']=(int)SUCCESS;
				return $data_friends;
				
			}else{
				$obj -> rs = SUCCESS;
				return $obj;
			}
		}catch (Exception $e){
			$obj -> rs = FAILED;
			$obj -> errcode = "01000000";
			return $obj;
		}
	}
}

?>