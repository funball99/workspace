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

?>