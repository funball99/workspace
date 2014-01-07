<?php 
require_once ('../../../source/class/class_core.php');
require_once ('../../../source/discuz_version.php');

define ( 'CONFIG', '../../config/config_global.php' );
require_once '../tools.php';
if (DISCUZ_VERSION == 'X2'){
	$thisUrl=dirname('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'/../../install/connection.php?Apk_pack_pwd='.$_REQUEST['Apk_pack_pwd'];
	//header("Location:$thisUrl");
	echo file_get_contents($thisUrl);exit;
}else{
	require_once 'connection_x25.php';
}
?>