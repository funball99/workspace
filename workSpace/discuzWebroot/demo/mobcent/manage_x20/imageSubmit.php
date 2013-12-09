<?php 
ob_end_clean ();
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

$title = $_POST['title'];
$title = unicode_encode($title);
$title = str_replace("\\","\\\\",$title);

$img = $_POST['img'];
$imgUrl = $_POST['imgUrl'];
$cidtype = $_POST['link'];
$cid = $_POST['linkUrl'];
$orderby = $_POST['orderby'];
$time=time();
 
if($_GET['act']=="add"){ 
	 
$data=DB::query("INSERT INTO ".DB::table('add_portal_module')." (cid,cidtype,imgval,imgtype,title,isimage,display,time) VALUES ('$cid','$cidtype','$imgUrl','$img','$title',1,'$orderby','$time')");
if(isset($data)){
   echo '<script>alert(decodeURI("%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F"));location.href="index.php";</script>';
}else{
   echo '<script>location.href="index.php";</script>';
}
	 
}elseif($_GET['act']=="modify"){
	$modifyid=isset($_GET[imgId])?intval($_GET[imgId]):"";
	$res=DB::query("update ".DB::table('add_portal_module')." set cid='$cid',cidtype='$cidtype',imgval='$imgUrl',imgtype='$img',title='$title',display='$orderby' where id = $modifyid");
	if(isset($res)){
	   echo '<script>alert(decodeURI("%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F"));location.href="index.php";</script>';
	}else{
	   echo '<script>location.href="index.php";</script>';
	}
}elseif($_GET['act']=="del"){
	$romoveid=isset($_GET[id])?intval($_GET[id]):"";
	$rst=DB::query("delete from ".DB::table('add_portal_module')." where id = $romoveid and isimage= 1");
	if($rst==1){
		echo "<script>alert(decodeURI('%E5%88%A0%E9%99%A4%E6%88%90%E5%8A%9F'));			
			location.href='index.php'</script>";
	}else{
		echo "<script>alert(decodeURI('%E5%88%A0%E9%99%A4%E5%A4%B1%E8%B4%A5%EF%BC%8C%E8%AF%B7%E9%87%8D%E8%AF%95%EF%BC%81'));			
			location.href='index.php'</script>";
	}
}


?>