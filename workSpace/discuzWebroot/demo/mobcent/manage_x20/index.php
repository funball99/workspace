<?php
ob_end_clean ();
session_start(); 

$session_file='session.txt';
$session_time=3600; //10 minutes
if(!file_exists($session_file)){
	fopen("$session_file", "w+"); 
	file_put_contents($session_file, time());
}
$lasttime = file_get_contents($session_file);
if(time()-$lasttime>$session_time){
	unlink($session_file);
	unset($_SESSION["admin"]);
	$_SESSION["admin"] = false;	
}else{	 
	file_put_contents($session_file, time());
}
 


require_once '../tool/tool.php';

if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
	require_once '../Config/dynamicobject.php';
	require_once './'.dynamicobject :: getShortDymanicObject('manageImpl').'.php';
	$className = dynamicobject :: getShortDymanicObject('manageImpl');
	$class=new ReflectionClass($className);
	$classObj = $class->newInstance();
	$obj = $classObj->getManageObj();
}else{ 
	echo "<script>location.href='login.php';</script>";
}
 
 

 
?>