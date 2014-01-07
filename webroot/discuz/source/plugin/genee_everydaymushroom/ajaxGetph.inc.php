<?php
/**
 *      [G1 Studio] (C)2012-2013.
 *
 *      $Id: ajaxGetdt.inc.php 29558 2013-03-07 11:15 genee $
 */

if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}

if (empty ( $_SERVER ['HTTP_REFERER'] )) {
	exit ( 'Access Denied' );
}
require_once 'source/plugin/genee_everydaymushroom/include/genee.inc.php';

if ($_G ['genee_formhash'] != FORMHASH) {
	showmessage ( 'undefined_action' );
}

$checkphdb = "SELECT table_name FROM information_schema.TABLES WHERE TABLE_SCHEMA='" . $_G ['config'] ['db'] [1] ['dbname'] . "' and table_name='" . DB::table ( 'genee_mush_ph' ) . "'";

$pharr = DB::fetch_first ( $checkphdb );
if (! empty ( $pharr )) {
	
	if (! defined ( "IN_MOBILE" )) {
		
		$mushph = DB::fetch_all ( "SELECT username, sum( credit ) as credit , create_time
	FROM " . DB::table ( 'genee_mush_ph' ) . "
	where MONTH( FROM_UNIXTIME( create_time ) ) = MONTH( CURDATE( ) ) 
	AND WEEK( FROM_UNIXTIME( create_time ) ) = WEEK( CURDATE( ) ) 
	GROUP BY username
	ORDER BY credit DESC 
	LIMIT 12  " );
		$return = "";
	
	} else {
		
		$checkdb = "SELECT table_name FROM information_schema.TABLES WHERE TABLE_SCHEMA=%s and table_name=%s";
		$checkcopyright1 = DB::fetch_first ( $checkdb, array ($_G ['config'] ['db'] [1] ['dbname'], DB::table ( 'genee_everydaymushroom_mobile_v' ) ) );
		if (! empty ( $checkcopyright1 )) {
			$mobile = DB::fetch_first ( "SELECT * FROM %t  ", array ('genee_everydaymushroom_mobile_v' ) );
			$mushph = DB::fetch_all ( "SELECT username, sum( credit ) as credit , create_time
	FROM " . DB::table ( 'genee_mush_ph' ) . "
	where MONTH( FROM_UNIXTIME( create_time ) ) = MONTH( CURDATE( ) ) 
	AND WEEK( FROM_UNIXTIME( create_time ) ) = WEEK( CURDATE( ) ) 
	GROUP BY username
	ORDER BY credit DESC 
	LIMIT $mobile[phbxss]  " );
		}
		
		$return = "";
	
	}
	foreach ( $mushph as $k => $v ) {
		$return .= "<li class=\"phli\">" . "<span>" . $geneelang ['d'] . "<font class=\"fdm\">" . ( int ) ($k + 1) . "</font>" . $geneelang ['m'] . "</span>" . "<span>" . $v ['username'] . "</span>" . "<span class=\"cspan\">" . $v ['credit'] . "</span>" . "</li>";
	}
}

echo $return;

?>