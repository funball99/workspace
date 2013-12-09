<?php
require_once './abstarctMsgReply.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../uc_client/client.php';

class msgReplyImpl_x20 extends abstarctMsgReply {
	public function getmsgReplyObj() {
		
		}

}

?>