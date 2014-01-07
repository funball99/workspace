<?php
define('UC_API', true);
define('IN_MOBCENT',1);
require_once '../../source/class/class_core.php';
require_once '../../uc_client/client.php';
require_once '../../uc_client/model/base.php';
require_once '../../uc_client/model/user.php';
require_once '../tool/tool.php';
require_once '../../config/config_ucenter.php';
require_once DISCUZ_ROOT.'./uc_client/client.php';
require_once DISCUZ_ROOT.'./source/function/function_member.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once ('./abstractWebLogin.php');
require_once '../Config/public.php';
class webloginImpl_x25 extends abstractWebLogin {
	
	function login() {
		$u = $_GET['u'];
		$p = $_GET['p'];
		
		}
	
}

?>