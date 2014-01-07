<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
<script type="text/javascript" src="images/check.js"></script> 
<?php

ob_end_clean ();
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../model/table_common_member_profile.php';
require_once '../../config/config_ucenter.php';
require_once '../Config/public.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile ( 'function/forumlist' );

$romoveid=isset($_GET[id])?intval($_GET[id]):"";
$news_list = DB::fetch(DB::query("SELECT count(*) as num FROM ".DB::table('add_portal_module')." where mid=".$romoveid." AND isimage=0"));

$data=DB::query("delete from ".DB::table('add_portal_module')." where mid = $romoveid AND isimage=0");
$data=DB::query("delete from ".DB::table('add_module')." where id = $romoveid ");
if(isset($data))
{
	echo "<script> alert(decodeURI('%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F'));window.location='index.php';</script>";
}
 
