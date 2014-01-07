<?php
require_once './abstarctCommonLocation.php';
define ( 'IN_MOBCENT', 1 );
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../model/table/x20/mobcentDatabase.php';

class commonLocationImpl_x20 extends abstarctCommonLocation {
	public function getCommonLocationObj() {
		
		 $accessSecret = $_GET['accessSecret'];
		 $accessToken = $_GET['accessToken'];
		 $info = new mobcentGetInfo();
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
		 $userId = $arrAccess['user_id'];
		 if(empty($userId))
		 {
		 	return C::t('common_member') -> userAccessError();
		 	exit();
		 }
		$longitude =$_GET['longitude'];	 
		$latitude =	$_GET['latitude'];	 
		$location	=	echo_urldecode ($_GET['location']);	 
		
		$right = (int)1;
		$threadlist =  C::t('home_surrounding_user') ->fetch_num_by_userid($userId);
		if($threadlist != 0)
		{
			$data = C::t('home_surrounding_user') -> fetch_update_by_userid($longitude,$latitude,$location,$userId);
			if($data)
			{
				$data_post['rs'] = 1;
				return $data_post;
				exit();
			}
			else
			{
				$data_post['rs'] = 0;
				return $data_post;
				exit();
			}
		}
		else
		{
			$insert = C::t('home_surrounding_user') -> fetch_insert_by_userid($longitude,$latitude,$location,$userId);
			if($insert)
			{
				$data_post['rs'] = 1;
				return $data_post;
				exit();
			}
			else
			{
				$data_post['rs'] = 0;
				return $data_post;
				exit();
			}
		}
		}

}

?>
