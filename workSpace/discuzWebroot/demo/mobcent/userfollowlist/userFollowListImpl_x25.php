<?php
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../model/table_common_member_profile.php';
require_once '../tool/constants.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once ('./abstractUserFollowList.php');
class userFollowListImpl_x25 extends abstractUserFollowList {
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
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$uid =$_G ['uid']= $arrAccess['user_id'] == $_REQUEST ['userId']? intval($arrAccess['user_id']):intval ( $_REQUEST ['userId']); 
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		$page 	= $_GET['page'] ? $_GET['page']:1;
		$limit 	= $_GET['pageSize']?$_GET['pageSize']:10;							 
		$start 	= ($page-1) * $limit ;
		$end 	= $start+$limit;		
		$followes = C::t('home_follow')->fetch_all_following_by_uid($uid,'',$start, $limit);
		$data_friends = array();
		$count= C::t('home_follow')->count_follow_user($uid);
		if(!$count){
			echo '{"rs":0}';exit();
		}
		if(C::t('home_blacklist')->count_by_uid_buid('',$uid)){
			$query = DB::query('SELECT uid FROM %t WHERE buid=%d', array('home_blacklist', $uid));
			while($value = DB::fetch($query)) {
				$data_black[] = $value['uid'];
			}
		}
		$isblack =0;
		foreach($followes as $k=>$followe){
				$query = DB::query( "SELECT COUNT(*) AS num  FROM %t WHERE buid=%d and uid = %d", array (
						"home_blacklist",
						$uid,
						$followe["followuid"]
						
				) );
				while($value = DB::fetch($query)) {
					if($value['num'])
					{
						$isblack = 1;
					}
				
			$uids[]=$followe["followuid"];
			$data_friends['uid']=(int)$followe['uid'];
			$data_friends['username']=$followe['username'];
			$data_friends['icon_url']='http://192.168.137.1';
			
			$data['userId']		=(int)$followe["followuid"];
			$data['uid']		=(int)$followe["followuid"];
			$data['name']		=$followe["fusername"];
			$data['status']		=(int)$followe["status"];
			$data['is_black']	=0;
			$data['gender']		=$followe["gender"];
			$data['icon']		=userIconImg($followe['followuid']);
			$data_friends['list'][$followe["followuid"]]=$data;
			$isblack = 0;
				}
				
			}
		if(!empty($uids)){
			$member_status_data = C::t('common_session')->fetch_all_by_uid($uids, $start,$limit);
			foreach($member_status_data as $k=>$v){
				$member_status[$v['uid']]=empty($v['invisible'])?1:0;
			}
		
			$profile = new memberProfile();
			if(count($uids) && !empty($uids)){
				$list = $profile -> get_profile_by_uid($uids,'gender',$start,$limit);
			}else{
		
			}
			$total =0;
			foreach($list as $k=>$v){
				$data_friends['list'][$v['uid']]['status'] = $member_status[$v['uid']];
				$data_friends['list'][$v['uid']]['gender'] =(int) $v['gender'];
				$data_friends['list'][$v['uid']]['level'] =(int) $v['stars'];
				$data_friends['list'][]=$data_friends['list'][$v['uid']];
				$data2[]=$data_friends['list'][$v['uid']];
				$total =$total+1;
			}
			$data_friends['list'] = $data2;
			$N = ceil($count/$limit);
			$data_friends['icon_url']=DISCUZSERVERURL;
			$data_friends['has_next'] = ($page>=$N || $N==1) ?0:1; 
			$data_friends['page'] = (Int)$page;
			$data_friends['rs']=(int)SUCCESS;
			return  $data_friends;
		}
		else
		{
			$obj -> rs = FAILED;
			$obj -> errcode = "01000000";
			return $obj;
		}
		}

}

?>