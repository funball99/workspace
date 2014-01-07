<?php  
require_once ('../../source/class/class_core.php');
require_once ('../../source/discuz_version.php');
define ( 'ROOT_PATH', dirname ( __FILE__ ) . '/../' );
define ( 'CONFIG', '../config/config_global.php' );
if (DISCUZ_VERSION == 'X2'){
	echo "<script>location.href='index_old.php';</script>";
}else{
	echo "<script>alert('please turn to new page...');location.href='../index/index.php';</script>";
}
 
?>