<?php
 
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../source/function/function_admincp.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
$setting = C::t('common_setting')->fetch_all(null);
 
$data=array(    
		"rs"=>1,
		"aboutInfo"=>array(
				"enterprise_desc"=>$setting['bbname'],
				"enterprise_email"=> $setting['adminemail'],
				"enterprise_qq"=>"1350469104445",
				"enterprise_tel"=>"010-59120987",
				"enterprise_website"=>$setting['siteurl'],
				"enterprise_weibo_qq"=>"http://www.qq.com/",
				"enterprise_weibo_sina"=>"http://www.weibo.com"
				)
		);
echo echo_json($data);