<?php 
ob_end_clean ();
require_once '../../Config/public.php';
require_once '../../public/mobcentDatabase.php';
require_once '../../../source/class/class_core.php';
C::app ()->init();
@session_start();
if(isset($_POST['uname']) && isset($_POST['pwd'])){
	$uname=$_POST['uname'];
	$pwd=md5($_POST['pwd']);
	$query = DB::query("SELECT * FROM ".DB::table('add_admin')." where id = 1");
	while($list = DB::fetch($query)) {
		$result[] = $list;
	}
	if($uname==$result[0][username] && $pwd==$result[0][password]){
		$_SESSION['renxing']=true;
		echo "<script>location.href='../index.php';</script>";
	}else{
		echo "<script>alert(decodeURI('%E7%94%A8%E6%88%B7%E5%90%8D%E6%88%96%E5%AF%86%E7%A0%81%E4%B8%8D%E6%AD%A3%E7%A1%AE%EF%BC%81'));location.href='login.php';</script>";
	}
}else{
	echo "<script>alert(decodeURI('%E8%AF%B7%E9%80%9A%E8%BF%87%E6%AD%A3%E7%A1%AE%E6%96%B9%E5%BC%8F%E6%8F%90%E4%BA%A4%EF%BC%81'));location.href='login.php';</script>";
}

?>
 
