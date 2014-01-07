<?php 
ob_end_clean ();
define ( 'IN_MOBCENT', 1 );
require_once '../../../source/class/class_core.php';
require_once '../../../source/function/function_forum.php';
require_once '../../model/table_common_member_profile.php';
require_once '../../../config/config_ucenter.php';
require_once '../../Config/public.php';
require_once '../tools.php';
require_once libfile ( 'function/forumlist' );
define('ALLOWGUEST', 1);
C::app ()->init ();
@session_start();
if(isset($_SESSION['renxing']) && $_SESSION['renxing']==true){
	if(empty($_REQUEST['lid'])){
		echo "<script> alert(decodeURI('%E8%AF%B7%E9%80%89%E6%8B%A9%E8%A6%81%E5%88%A0%E9%99%A4%E7%9A%84%E6%95%B0%E6%8D%AE%EF%BC%81'));history.back();</script>";
		exit();
	}
	$mid=$_REQUEST['mid'];
	$lid=$_REQUEST['lid'];
	if(!empty($lid) && is_array($lid)){
		$lid = implode(',',$lid);
	}
	$data = DB::query('delete from %t where id in('.$lid.')',array('add_portal_module'));
	if(isset($data))
	{
		echo "<script> alert(decodeURI('%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F'));window.location.href='moduleList.php?mid=$mid'</script>";
	}
}else{
	echo "<script>location.href='../login/login.php';</script>";
}
?>