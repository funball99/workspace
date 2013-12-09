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
echo "<script type='text/javascript' src='images/check.js'></script> ";
 

$cidtype = $_POST['content']=='manual'?$_POST['link']:$_POST['automaticlink'];
$cid = $_POST['content']=='manual'?$_POST['linkUrl']:$_POST['automaticlinkUrl'];

if($_GET['act']=="add"){
	$mid =$_POST['mid'];
	$newstype_list = DB::query("SELECT * FROM ".DB::table('add_module')." where id=".$mid);
	while($value = DB::fetch($newstype_list)) {
		$data[] = $value;
	}
	$cidtype = $data[0]['content']==1?$_POST['link']:$_POST['automaticlink'];
	$cid = $data[0]['content']==1?$_POST['linkUrl']:$_POST['automaticlinkUrl'];
	$essence = $_POST['essence']=='essence'?1:2;
	$time=time();
	$result=DB::query("INSERT INTO ".DB::table('add_portal_module')." (mid,cid,cidtype,title,isimage,time,essence) VALUES ($mid,$cid,'$cidtype','',0,'$time',$essence)");
	if($result==1){
		echo "<script>alert(decodeURI('%E6%B7%BB%E5%8A%A0%E6%88%90%E5%8A%9F'));			
		history.back();</script>";
	}else{
		echo "<script>alert(decodeURI('%E6%B7%BB%E5%8A%A0%E5%A4%B1%E8%B4%A5%EF%BC%8C%E8%AF%B7%E9%87%8D%E8%AF%95%EF%BC%81'));			
		history.back();</script>";
	}
}elseif($_GET['act']=="del"){
	$romoveid=isset($_GET[id])?intval($_GET[id]):"";
	$rst=DB::query("delete from ".DB::table('add_portal_module')." where id = $romoveid");
	if($rst==1){
		echo "<script>alert(decodeURI('%E5%88%A0%E9%99%A4%E6%88%90%E5%8A%9F'));			
			history.back();</script>";
	}else{
		echo "<script>alert(decodeURI('%E5%88%A0%E9%99%A4%E5%A4%B1%E8%B4%A5%EF%BC%8C%E8%AF%B7%E9%87%8D%E8%AF%95%EF%BC%81'));			
			history.back();</script>";
	}
}elseif($_GET['act']=="delete"){
	$tids=$_POST['fid'];
	foreach($tids as $td){
		$res=DB::query("delete from ".DB::table('add_portal_module')." where id = $td");
	}	
	if($res==1){
		echo "<script>alert(decodeURI('%E5%88%A0%E9%99%A4%E6%88%90%E5%8A%9F'));			
			history.back();</script>";
	}else{
		echo "<script>alert(decodeURI('%E5%88%A0%E9%99%A4%E5%A4%B1%E8%B4%A5%EF%BC%8C%E8%AF%B7%E9%87%8D%E8%AF%95%EF%BC%81'));			
			history.back();</script>";
	}
} 

 

?>