<?php 
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../../source/function/function_forumlist.php';
require_once '../tool/tool.php';
require_once '../model/table/x20/topic.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
define('IN_MOBCENT',1);
require_once '../model/table/x20/mobcentDatabase.php';
require_once '../install/checkModule.php';

	$mid=$_GET['mid'];
	$newstype_list = DB::query("SELECT count(*) as num FROM ".DB::table('add_portal_module')." where isimage !=1 AND mid=".$mid);
	while($value = DB::fetch($newstype_list)) {
		$data[] = $value;
	}
	echo $data[0]['num'];
?>