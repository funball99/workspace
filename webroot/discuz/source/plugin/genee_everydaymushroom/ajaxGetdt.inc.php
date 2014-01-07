<?php
/**
 *      [G1 Studio] (C)2012-2013.
 *
 *      $Id: ajaxGetdt.inc.php 29558 2013-03-07 11:15 genee $
 */

if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}

require_once 'source/plugin/genee_everydaymushroom/include/genee.inc.php';

if ($_G ['genee_formhash'] != FORMHASH) {
	showmessage ( 'undefined_action' );
}

$checkdtdb = "SELECT table_name FROM information_schema.TABLES WHERE TABLE_SCHEMA='" . $_G ['config'] ['db'] [1] ['dbname'] . "' and table_name='" . DB::table ( 'genee_mush_log' ) . "'";
$dtarr = DB::fetch_first ( $checkdtdb );
if (! empty ( $dtarr )) {
	
	if (! defined ( "IN_MOBILE" )) {
		$mushdt = DB::fetch_all ( "SELECT username,log,create_time FROM " . DB::table ( 'genee_mush_log' ) . "  order by create_time desc limit 10  " );
		
		$return = "";
	
	} else {
		
		$checkdb = "SELECT table_name FROM information_schema.TABLES WHERE TABLE_SCHEMA=%s and table_name=%s";
		$checkcopyright1 = DB::fetch_first ( $checkdb, array ($_G ['config'] ['db'] [1] ['dbname'], DB::table ( 'genee_everydaymushroom_mobile_v' ) ) );
		if (! empty ( $checkcopyright1 )) {
			$mobile = DB::fetch_first ( "SELECT * FROM %t  ", array ('genee_everydaymushroom_mobile_v' ) );
			$mushdt = DB::fetch_all ( "SELECT username,log,create_time FROM " . DB::table ( 'genee_mush_log' ) . "  order by create_time desc limit $mobile[userdts]  " );
		
		}
		
		$return = "";
	}
	foreach ( $mushdt as $v ) {
		$return .= "<li>" . dgmdate ( $v ['create_time'], 'Y-m-d H:i:s' ) . "&nbsp;" . $v ['username'] . "&nbsp;" . $v ['log'] . "</li>";
	}
}

echo $return;

?>