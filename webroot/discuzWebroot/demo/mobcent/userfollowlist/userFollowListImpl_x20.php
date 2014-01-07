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
require_once '../../uc_client/client.php';
require_once ('./abstractUserFollowList.php');
require_once '../model/table/x20/mobcentDatabase.php';

class userFollowListImpl_x20 extends abstractUserFollowList {
	function getUserFollowList() {
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
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$fuid = $arrAccess['user_id'];
		if(empty($fuid))
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
		$page 	= $_GET['page'] ? $_GET['page']:1;
		$limit 	= $_GET['pageSize']?$_GET['pageSize']:10;						
		$start 	= ($page-1) * $limit ;	
		$end 	= $start+$limit;		
		$followes = C::t('home_follow')->fetch_all_following_by_uid($uid, $start, $limit);
		
		$data_friends = array();
		$count= C::t('home_follow')->count_follow_user($uid);
		if(!$count){
			echo '{"rs":0}';exit();
		}
		foreach($followes as $k=>$followe){
		
			$uids[]=$followe["fuid"];
			$data_friends['uid']=(int)$followe['uid'];
			$data_friends['icon_url']='http://192.168.137.1';
		
			$data['userId']		=(int)$followe["fuid"];
			$data['uid']		=(int)$followe["fuid"];
			$data['name']		=$followe["fusername"];
			$data['is_black']	=0;
			$data['is_follow']	=1;
			$data['gender']		=$followe["gender"];
			$data['icon']		=userIconImg($followe['fuid']);
			$data_friends['list'][$followe["fuid"]]=$data;
			}
			
		if(!empty($uids)){
           $member_status_data_info = C::t('home_friend') -> get_status_firend($uids);
           $member_status_data = C::t('home_friend') -> get_status_firend_status($uids);
           if ($member_status_data && is_array($member_status_data)){
           	foreach($uids as $key=>$val)
           	{
           		if(in_array($member_status_data, $val))
           		{
           			$status[$val] = empty($member_status_data_info[$val]['status'])?1:0;
           			
           		}
           		else {
           			$status[$val] =0;
           		}
           	}
           }
			$profile = new table_common_member_profile();
			if(count($uids) && !empty($uids)){
				$list = $profile -> get_profile_by_uid($uids,'gender');
			}else{
		
			}
			foreach($list as $k=>$v){
				$data_friends['list'][$v['uid']]['status'] = $status[$v['uid']];
				$data_friends['list'][$v['uid']]['gender'] =(int) $v['gender'];
				$data_friends['list'][$v['uid']]['level'] =(int) $v['stars'];
				$data_friends['list'][$v['uid']]['name'] = $v['username'];
				$data_friends['list'][]=$data_friends['list'][$v['uid']];
				$data2[]=$data_friends['list'][$v['uid']];
			}
			$data_friends['list'] = $data2;
			$N = ceil($count/$limit);
			$data_friends['icon_url']=DISCUZSERVERURL; 
			$data_friends['has_next'] = ($page>=$N || $N==1) ?0:1;  
			$data_friends['page'] = (Int)$page;
			$data_friends['rs']=(int)SUCCESS; 
			return $data_friends;
		}
		else
		{
			$data_friends['rs'] = FAILED;
			$data_friends['errcode']  = "01000000";
			return $data_friends;
		}
		}

}

?>