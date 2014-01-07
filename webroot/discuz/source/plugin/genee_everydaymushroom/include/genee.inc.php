<?php
/**
 *      [G1 Studio!] (C)2012-2013.
 *
 *      $Id: genee.inc.php 5473 2013-03-07 11:15 genee $
 */
if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}

loadcache ( 'plugin' );
$geneelang = lang ( 'plugin/genee_everydaymushroom' );
$gvar = $_G ['cache'] ['plugin'] ['genee_everydaymushroom'];

foreach ( $_POST as $k => $v ) {
	$_G ['genee_' . $k] = function_exists ( 'daddslashes' ) ? daddslashes ( dhtmlspecialchars ( $v ) ) : addslashes ( htmlspecialchars ( $v ) );
}

foreach ( $_GET as $k => $v ) {
	$_G ['genee_' . $k] = function_exists ( 'daddslashes' ) ? daddslashes ( dhtmlspecialchars ( $v ) ) : addslashes ( htmlspecialchars ( $v ) );
}
if (! function_exists ( 'isUTF8' )) {
	function isUTF8($str1) {
		if ($str === mb_convert_encoding ( mb_convert_encoding ( $str, "UTF-32", "UTF-8" ), "UTF-8", "UTF-32" )) {
			return true;
		} else {
			return false;
		}
	}
}
if (! function_exists ( 'getIp1' )) {
	function getIp1() {
		if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
			$ip = getenv ( "HTTP_CLIENT_IP" );
		else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
			$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
		else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
			$ip = getenv ( "REMOTE_ADDR" );
		else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
			$ip = $_SERVER ['REMOTE_ADDR'];
		else
			$ip = "unknown";
		return ($ip);
	}
}
?>