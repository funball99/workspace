<?php
require_once './abstarctCommonLocation.php';
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../model/table_surround_user.php';
require_once '../tool/tool.php';
require_once '../model/table_surround_user.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();

class commonLocationImpl_x25 extends abstarctCommonLocation {
	public function getCommonLocationObj() {
		$info = new mobcentGetInfo();
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
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$userId = $arrAccess['user_id'];
		if(empty($userId))
		{
			return $info -> userAccessError();
			exit();
		}
		
		$longitude =$_GET['longitude'];	 
		$latitude =	$_GET['latitude'];	 
		$location	=	echo_urldecode($_GET['location']);	 
		
		$right = (int)1;
		$threadlist = surround_user:: fetch_num_by_userid($userId);
		if($threadlist != 0)
		{
			$data = surround_user::fetch_update_by_userid($longitude,$latitude,$location,$userId);
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
			$insert = surround_user:: fetch_insert_by_userid($longitude,$latitude,$location,$userId);
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