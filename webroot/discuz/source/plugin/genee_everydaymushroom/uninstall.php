<?php
/**
 *      [G1 Studio^_^] (C)2012-2013.
 *
 *      $Id: uninstall.php 29558 2013-03-07 11:15 genee $
 */
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

DB::query("DROP TABLE IF EXISTS ".DB::table('genee_mush_user')."");
DB::query("DROP TABLE IF EXISTS ".DB::table('genee_mush_log')."");
DB::query("DROP TABLE IF EXISTS ".DB::table('genee_mush_ph')."");
DB::query("DROP TABLE IF EXISTS ".DB::table('genee_mush_km')."");


@updatecache(array('setting', 'styles'));
$finish = TRUE;

?>