<?php
/**
 *      [G1 Studio!] (C)2012-2013.
 *
 *      $ID: 5473 genee 
 *      $QQ: 403172306
 */
if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}

loadcache ( 'plugin' );
$geneelang = lang ( 'plugin/genee_wxgzpt' );
$gvar = $_G ['cache'] ['plugin'] ['genee_wxgzpt'];

foreach ( $_POST as $k => $v ) {
	$_G ['genee_' . $k] = function_exists ( 'daddslashes' ) ? daddslashes ( dhtmlspecialchars ( $v ) ) : $v;
}

foreach ( $_GET as $k => $v ) {
	$_G ['genee_' . $k] = function_exists ( 'daddslashes' ) ? daddslashes ( dhtmlspecialchars ( $v ) ) : $v;
}
 
function order_sn($uid){
	$r=rand(1,100);
	$v_oid =$r.$uid.date('His',time());
	return $v_oid;
}

function isUTF8($str1) {
	if ($str === mb_convert_encoding ( mb_convert_encoding ( $str, "UTF-32", "UTF-8" ), "UTF-8", "UTF-32" )) {
		return true;
	} else {
		return false;
	}
}

?>