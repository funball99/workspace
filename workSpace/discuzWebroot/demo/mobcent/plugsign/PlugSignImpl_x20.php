<?php
require_once './abstractPlugSign.php';
define ( 'IN_MOBCENT', 1 );
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/constants.php';

require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'class/credit' );
require_once libfile ( 'function/post' );
require_once libfile ( 'function/forum' );
require_once '../model/table/x20/mobcentDatabase.php';

class PlugSignImpl_x20 extends abstractPlugSign {  
	public function getPlugSignObj() {
		$info = new mobcentGetInfo ();
		$rPostion = $_GET['r'] ? $_GET['r']:0;   
		$longitude =$_GET['longitude']; 
		$latitude =	$_GET['latitude'];	 
		$location	=	echo_urldecode($_GET['location']);	 
		$aid = $_REQUEST ['aid'];   
		$aid_Img=explode(',',$aid);
		$readperm = 0;
		$price = 0;
		$typeid = 0;
		$sortid = 0;
		$displayorder = 0;
		$digest = 0;
		$special = 0;
		$attachment = 0;
		$moderated = 0;
		$thread ['status'] = 0;
		$isgroup = 0;
		$replycredit = 0;
		$closed = 0;
		$publishdate = time ();
		$fid = $_G ['fid'] = $_GET ['boardId'];
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
		$uid = $_G ['uid'] = $arrAccess['user_id'];
		$userInfoId = getuserbyuid ( $uid );
		
		$author = $username = $userInfoId ['username'];
		require_once '../model/table/x20/mobcentDatabase.php';
		$info = new mobcentGetInfo ();
		$modnewposts = $info ->getBoard($_G ['fid']);
		$displayorder = $modnewposts['modnewposts'] > 0?-2:0;
		$space = $info->getUserInfo ( intval ( $uid ) );
		if(empty($_G ['uid']))
		{
			return C::t('common_member') -> userAccessError();
			exit();
			
		}
		if(empty($space) || !$space)
		{
			$data_post ["rs"] = 0;
			$data_post ["error"] = '01010005';
			return $data_post;
			exit();
		}
		$author = $space ['username'];
		$_G ['username'] = $lastposter = $author;
		$_G = array_merge ( $_G, $space );
		
		$data_post['rs'] = 0;
		$data_post['errcode'] = '04000057';/*2.0 bu zhi chi qian dao*/
		return $data_post;
		exit();
		
	}

}

?>