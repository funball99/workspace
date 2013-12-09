<?php 

ob_end_clean ();
define('IN_MOBCENT',1);
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once libfile('function/forumlist');
require_once '../tool/tool.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once '../model/table/x25/table_add_portal_module.php';
	$mid=$_GET['mid'];
	
	$data = add_portal_module::check_module_list_count($mid);
	echo $data['num'];
?>