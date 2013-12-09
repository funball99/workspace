<?php       
ob_end_clean ();
define ( 'IN_MOBCENT', 1 );
require_once '../../../source/class/class_core.php';
require_once '../../../source/function/function_forum.php';
require_once '../../model/table_common_member_profile.php';
require_once '../../../config/config_ucenter.php';
require_once libfile ( 'function/forumlist' );
require_once '../../Config/public.php';
require_once '../tools.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
@session_start();
if(isset($_SESSION['renxing']) && $_SESSION['renxing']==true){
	if(empty($_GET['id'])){
		echo "<script> alert(decodeURI('%E8%AF%B7%E9%80%89%E6%8B%A9%E8%A6%81%E5%88%A0%E9%99%A4%E7%9A%84%E6%95%B0%E6%8D%AE%EF%BC%81'));history.back();</script>";
		exit();
	}
	$id = isset($_GET['id'])?intval($_GET['id']):"";
	$data=DB::query("delete from ".DB::table('add_portal_module')." where id = $id and isimage= 1");
	echo "<script> alert(decodeURI('%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F'));window.location='../manage.php';</script>";
}else{
	echo "<script>location.href='../login/login.php';</script>";
}