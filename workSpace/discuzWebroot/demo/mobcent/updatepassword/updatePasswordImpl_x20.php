<?php
define('IN_MOBCENT',1);
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';

require_once ('./abstractUpdatePassword.php');
require_once '../model/table/x20/mobcentDatabase.php';
class updatePasswordImpl_x20 extends abstractUpdatePassword {
	function updatePassword() {
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
		$userId = $arrAccess['user_id'];
		if(empty($userId))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		$space = $info->getUserInfo(intval($userId));
		$_G=array_merge($_G,$space);
		$email = $_GET['email'] ?urldecode($_GET['email']): '';
		$oldpassword = $_GET['oldPwd'] ?urldecode($_GET['oldPwd']): '';
		$newpassword = $_GET['password'] ?urldecode($_GET['password']): '';
		$ignorepassword = 0;
		$ucresult = uc_user_edit(addslashes($space['username']), $oldpassword, $newpassword, $email);
		$newpwd = substr(md5($newpassword.'mobcent'),0,-3);
		
		if($ucresult<0){
			$data['rs']=0;
			$data['errcode']='01010008';
			return $data;
			exit();
		}
		if($ucresult >= 0){
			C::t('common_member') ->update_accessTopkent($accessToken,$newpwd,$userId,date('Y-m-d H:m:s'));
			$data['rs']= 1;
			$data['token'] = $accessToken;
			$data['secret'] = $newpwd;
			return $data;
			
		}
		}

}

?>