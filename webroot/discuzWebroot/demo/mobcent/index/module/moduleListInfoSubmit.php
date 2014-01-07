<?php 
ob_end_clean ();
define ( 'IN_MOBCENT', 1 );
require_once '../../../source/class/class_core.php';
require_once '../../../source/function/function_forum.php';
require_once '../../model/table_common_member_profile.php';
require_once '../../model/table/x25/table_add_portal_module.php';
require_once '../../../config/config_ucenter.php';
require_once libfile ( 'function/forumlist' );
require_once '../../Config/public.php';
require_once '../tools.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
@session_start();
if(isset($_SESSION['renxing']) && $_SESSION['renxing']==true){
	if(isset($_POST['mid'])){
		$link = $_POST['content']=='manual'?$_POST['link']:$_POST['automaticlink'];
		$linkUrl = $_POST['content']=='manual'?$_POST['linkUrl']:$_POST['automaticlinkUrl'];
		$mid =$_POST['mid'];
		$data = add_portal_module::check_module_edit($mid);
		
		$link = $data[0]['content']==1?$_POST['link']:$_POST['automaticlink'];
		$linkUrl = $data[0]['content']==1?$_POST['linkUrl']:$_POST['automaticlinkUrl'];
		$essence = $_POST['essence']=='essence'?1:2;
		
		$rows = DB::query('INSERT INTO %t VALUES(id,%d,%s,%s,%s,%s,%s,%d,%d,%s,%s)',array('add_portal_module',$mid,$linkUrl,$link,'','','',0,'',time(),$essence));
		if(isset($rows))
		{
			echo "<script> alert(decodeURI('%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F'));history.back();</script>";
		}
	}else{
		echo "<script>alert(decodeURI('%E8%AF%B7%E9%80%9A%E8%BF%87%E6%AD%A3%E7%A1%AE%E6%96%B9%E5%BC%8F%E6%8F%90%E4%BA%A4%EF%BC%81'));location.href='../manage.php';</script>";
	}
}else{
	echo "<script>location.href='../login/login.php';</script>";
}
?>