<?php
require_once './abstractPostImagePerm.php';
define('IN_MOBCENT',1);
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../model/table/x20/mobcentDatabase.php';

class postImagePermImpl_x20 extends abstractPostImagePerm {
	public function getpostImagePermImplObj() {
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
		$_G['uid'] = $uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		$info = new mobcentGetInfo();
		$space = $info->getUserInfo(intval($uid));
		$_G=array_merge($_G,$space);
		$_G['fid'] = intval($_GET['boardId']);
		$forum = $info->getForumSub($_G['fid']);
		$_G['forum']=array_merge($_G['forum'],$forum);
		
		require_once '../public/yz.php';
		$checkObj = new check();
		$resulst = $checkObj->postimageperm($_G['fid'],$forum['postperm']);
		if($resulst['error']){
			echo $resulst['message'];exit();
		}else{
			$data_post ["rs"] = 1;
		}
		
		return $data_post;
		}

}

?>