<?php
if ($_SERVER ['REQUEST_METHOD'] == 'POST' || $_GET ['echostr']) {
	define ( 'APPTYPEID', 127 );
	define ( 'CURSCRIPT', 'plugin' );
	define ( 'DISABLEXSSCHECK', true );
	
	$_GET ['id'] = 'genee_wxgzpt';
	
	require_once '../../../source/class/class_core.php';
	
	$discuz = C::app ();
	$cachelist = array ('plugin', 'diytemplatename' );
	
	$discuz->cachelist = $cachelist;
	$discuz->init ();
	
	define ( 'CURMODULE', 'genee_wxgzpt' );
	
	$_G ['siteurl'] = substr ( $_G ['siteurl'], 0, - 43 );
	$_G ['siteroot'] = substr ( $_G ['siteroot'], 0, - 43 );
	
	include DISCUZ_ROOT . './source/plugin/genee_wxgzpt/genee_wxgzpt.inc.php';
} else {
	echo 'Access Denied';
}

?>