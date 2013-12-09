<?php
ob_end_clean ();
require_once '../../Config/public.php';
require_once '../../public/mobcentDatabase.php';
require_once '../../../source/class/class_core.php';
C::app ()->init();
@session_start();
if(isset($_SESSION['renxing']) && $_SESSION['renxing']==true){	
	if(isset($_POST['oldpwd'])){		
		$oldpwd=md5($_POST['oldpwd']);
		$newpwd=md5($_POST['newpwd']);
		$renewpwd=md5($_POST['renewpwd']);
		
		$query = DB::query("SELECT * FROM ".DB::table('add_admin')." where id = 1");
		while($list = DB::fetch($query)) {
			$result[] = $list;
		}
		if($oldpwd != $result[0][password]){
			echo "<script>alert(decodeURI('%E6%82%A8%E8%BE%93%E5%85%A5%E7%9A%84%E5%8E%9F%E5%AF%86%E7%A0%81%E4%B8%8D%E6%AD%A3%E7%A1%AE%EF%BC%81'));location.href='../index.php';</script>";
			exit();
		} 
		if($newpwd != $renewpwd){
			echo "<script>alert(decodeURI('%E6%82%A8%E8%BE%93%E5%85%A5%E7%9A%84%E4%B8%A4%E6%AC%A1%E6%96%B0%E5%AF%86%E7%A0%81%E4%B8%8D%E4%B8%80%E8%87%B4%EF%BC%81'));location.href='../index.php';</script>";
			exit();
		}
		DB::query("UPDATE ".DB::table('add_admin')." SET password='".$newpwd."' where id = 1");
		echo "<script>alert(decodeURI('%E4%BF%AE%E6%94%B9%E6%88%90%E5%8A%9F%EF%BC%81'));location.href='../index.php';</script>";
	}else{
		echo "<script>alert(decodeURI('%E8%AF%B7%E9%80%9A%E8%BF%87%E6%AD%A3%E7%A1%AE%E6%96%B9%E5%BC%8F%E6%8F%90%E4%BA%A4%EF%BC%81'));location.href='../index.php';</script>";
	}
}else{
	echo "<script>alert(decodeURI('%E8%AF%B7%E6%82%A8%E5%85%88%E7%99%BB%E5%BD%95%EF%BC%81'));location.href='login.php';</script>";
}
?>
 
