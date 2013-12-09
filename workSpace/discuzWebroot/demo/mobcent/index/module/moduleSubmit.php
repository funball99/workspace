<?php         
ob_end_clean ();
define ( 'IN_MOBCENT', 1 );
require_once '../../../source/class/class_core.php';
require_once '../../../source/function/function_forum.php';
require_once '../../model/table_common_member_profile.php';
require_once '../../../config/config_ucenter.php';
require_once '../../Config/public.php';
require_once libfile ( 'function/forumlist' );
require_once '../tools.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
@session_start();
if(isset($_SESSION['renxing']) && $_SESSION['renxing']==true){
	if(isset($_POST['title']) && isset($_POST['content'])){
	    $title=$_POST['title'];
		$title = unicode_encode($title);	 
		$title = str_replace("\\","\\\\",$title);
	 
		$orderby = $_POST['orderby'];
		$content =$_POST['content']=='manual'?1:2;
		$data=DB::query("INSERT INTO ".DB::table('add_module')." (mname,display,content) VALUES ('$title',$orderby,$content)");
	    if(isset($data)){
		   echo '<script>alert(decodeURI("%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F"));location.href="../manage.php";</script>';
		}else{
		   echo '<script>location.href="../manage.php";</script>';
		}
	}else{
		echo "<script>alert(decodeURI('%E8%AF%B7%E9%80%9A%E8%BF%87%E6%AD%A3%E7%A1%AE%E6%96%B9%E5%BC%8F%E6%8F%90%E4%BA%A4%EF%BC%81'));location.href='../manage.php';</script>";
	}
}else{
	echo "<script>location.href='../login/login.php';</script>";
}
?>