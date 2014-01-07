<?php
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../model/table/x20/table_common_member_profile.php';
require_once '../tool/constants.php';
require_once '../public/common_json.php';
require_once '../model/table/x20/table_home_friend.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../model/table/x20/mobcentDatabase.php';
require_once '../../uc_client/client.php';
require_once ('./abstractFollowUserList.php');
class followUserListImpl_x20 extends abstractFollowUserList {
	function getFollowUserList() {
		$info = new mobcentGetInfo ();
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
		else
		{
			$uid =$_G ['uid']= empty($_GET ['userId'])?$arrAccess['user_id']:$arrAccess['user_id'] == $_GET ['userId']? intval($arrAccess['user_id']):intval ( $_REQUEST ['userId']);  
			if(empty($uid))
			{
				return C::t('common_member') -> userAccessError();
				exit();
			}
		}
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 10;  
		$start = ($page - 1) * $limit; 
		$end = $start + $limit;  
		$followes =C::t('home_follow')->fetch_all_by_followuid ( $uid, $start, $limit );
		$data_friends = array ();
		$count = C::t('home_follow')->count_follow_user ( $uid,1);
		foreach ( $followes as $k => $followe ) {
			$uids [] = $followe ["uid"];
			$data_friends ['uid'] = $followe ["uid"];
			$data_friends['icon_url']='http://192.168.137.1';
			
			$data_friends ['list'] [$followe ['fuid']]['userId']		=(int)$followe["fuid"];
			$data_friends ['list'] [$followe ['fuid']]['uid']		=(int)$followe["fuid"];
			$data_friends ['list'] [$followe ['fuid']]['name']		=$followe["fusername"];
			$data_friends ['list'] [$followe ['fuid']]['is_black']	=0;
			$data_friends ['list'] [$followe ['fuid']]['gender']		=$followe["gender"]; 
			$data_friends ['list'] [$followe ['fuid']]['icon']		=userIconImg($followe['fuid']);
		}
		if(!empty($uids)){
			$member_status_data = C::t('home_friend') -> get_status_firend($uids);
			foreach ($member_status_data as $v){
				$status[$v['uid']] = empty($v['status'])?1:0;
			}
			$profile = new table_common_member_profile ();
			$list = $profile->get_profile_by_uid ( $uids, 'gender' );
			foreach ( $list as $k => $v ) {
				$follow_num = DB::fetch_first ( "SELECT count(*) as count FROM ".DB::table('home_friend')." WHERE uid=".$uid.' AND fuid = '.$v ['uid']);
				$data_friends ['list'] [$v ['uid']] ['is_black']=0;
				$data_friends ['list'] [$v ['uid']]['is_friend'] = $follow_num ['count'] > 0?( int ) 1:( int ) 0;  
				$data_friends ['list'] [$v ['uid']] ['userId']=$v ['uid'];
				$data_friends ['list'] [$v ['uid']] ['uid']=$v ['uid'];
				$data_friends ['list'] [$v ['uid']] ['status'] = $status [$v ['uid']];
				$data_friends ['list'] [$v ['uid']] ['gender'] = $v ['gender'];
				$data_friends ['list'] [$v['uid']]['level'] =(int) $v['stars'];
				$data_friends ['list'] [$v['uid']]['name'] = $v['username'];
				$data_friends ['list'] [$v['uid']]['icon'] = userIconImg($v['uid']);
				$data_friends ['list'] [] = $data_friends ['list'] [$v ['uid']];
				$data2 [] = $data_friends ['list'] [$v ['uid']];
			}
			$data_friends ['icon_url'] = DISCUZSERVERURL;  
			$data_friends ["img_url"] = $url;
			$data_friends ['list'] = $data2;
			$N = ceil ( $count / $limit );
			$data_friends ['page'] = (Int)$page;
			$data_friends ['count'] = (Int)$count;
			$data_friends ['has_next'] = ($page >= $N || $N == 1) ? 0 : 1;  
			$data_friends['rs']=(int)SUCCESS;
			return  $data_friends;
		}
		else
		{
			$data_friends['rs']=(int)SUCCESS;
			return $data_friends;
		}
		}

}

?>