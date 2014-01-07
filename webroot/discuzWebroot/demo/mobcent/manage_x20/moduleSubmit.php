<?php
header("Content-Type:text/html;charset=utf-8");
define ( 'IN_MOBCENT', 1 );
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/constants.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
  
	$title=$_POST['title'];
	$title = unicode_encode($title);	 
	$title = str_replace("\\","\\\\",$title);
	
	$orderby = $_POST['orderby'];
	$content =$_POST['content']=='manual'?1:2;

if($_GET['act']=="add"){
	$result=DB::query("INSERT INTO ".DB::table('add_module')." (mname,display,content) VALUES ('$title',$orderby,$content)");
	if(isset($result)){
	   echo '<script>alert(decodeURI("%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F"));location.href="index.php";</script>';
	}else{
	   echo '<script>location.href="index.php";</script>';
	}
}elseif($_GET['act']=="modify"){
	$modifyid=isset($_REQUEST[id])?intval($_REQUEST[id]):"";
	$res=DB::query("update ".DB::table('add_module')." set mname='$title',display=$orderby where id = $modifyid");
	if($res==1){
		echo "<script>alert(decodeURI('%E7%BC%96%E8%BE%91%E6%88%90%E5%8A%9F'));			
			location.href='index.php'</script>";
	}else{
		echo "<script>alert(decodeURI('%E7%BC%96%E8%BE%91%E5%A4%B1%E8%B4%A5%EF%BC%8C%E8%AF%B7%E9%87%8D%E8%AF%95%EF%BC%81'));			
			location.href='index.php'</script>";
	}
}elseif($_GET['act']=="del"){
	$romoveid=isset($_GET[id])?intval($_GET[id]):"";
	$data=DB::query("delete from ".DB::table('add_portal_module')." where mid = $romoveid AND isimage=0");
	$rst=DB::query("delete from ".DB::table('add_module')." where id = $romoveid ");
	if($rst==1){
		echo "<script>alert(decodeURI('%E5%88%A0%E9%99%A4%E6%88%90%E5%8A%9F'));
		location.href='index.php'</script>";
	}else{
		echo "<script>alert(decodeURI('%E5%88%A0%E9%99%A4%E5%A4%B1%E8%B4%A5%EF%BC%8C%E8%AF%B7%E9%87%8D%E8%AF%95%EF%BC%81'));
		location.href='index.php'</script>";
	}
	
} 




?>