<?php
/**
 *      [G1 Studio^_^] (C)2012-2013.
 *
 *      $Id: ajaxHandle.inc.php 29558 2013-03-07 11:15 genee $
 */

if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}
global $_G;
require_once 'source/plugin/genee_everydaymushroom/include/genee.inc.php';
$credittype = $_G ['setting'] ['extcredits'] [$gvar ['exttype']] ['title'];
$gvar ['visualkm'] = DB::result_first ( "select value from " . DB::table ( 'common_pluginvar' ) . " where variable='visualkm' and displayorder='21'" );

if ($_G ['genee_formhash'] != FORMHASH) {
	showmessage ( 'undefined_action' );
}
if ($gvar ['closecmg']) {
	echo base64_encode ( $gvar ['closect'] . "&&&&close" );
	exit ();
}

$r = DB::fetch_first ( "SELECT * FROM " . DB::table ( "common_member_count" ) . " WHERE uid='$_G[uid]'" );

if ($r)
	$credit = $r ['extcredits' . $gvar ['exttype']];

if ($credit <= 0) {
	echo base64_encode ( $geneelang ['nocredit'] . $_G ['setting'] ['extcredits'] [$gvar ['credittype']] ['title'] . $geneelang ['nocredit1'] . "&&&&nocredit" );
	exit ();
}

$playnum = DB::result_first ( "select playnum from " . DB::table ( 'genee_mush_user' ) . " where uid='$_G[uid]'" );
if ($playnum <= 0) {
	echo base64_encode ( $gvar ['getmushnone'] . "&&&&noplaynum" );
	exit ();
}
//lv
$mushroomalv = array ('id' => 'a', 'lv' => $gvar ['mushroomalv'], 'point' => $gvar ['mushroomap'], 'name' => $gvar ['mushrooma'], 'pic' => $gvar ['mushroomapic'], 'type' => '0', 'comment' => $gvar ['mushroomact'] );
$mushroomblv = array ('id' => 'b', 'lv' => $gvar ['mushroomblv'], 'point' => $gvar ['mushroombp'], 'name' => $gvar ['mushroomb'], 'pic' => $gvar ['mushroombpic'], 'type' => '0', 'comment' => $gvar ['mushroombct'] );
$mushroomclv = array ('id' => 'c', 'lv' => $gvar ['mushroomclv'], 'point' => $gvar ['mushroomcp'], 'name' => $gvar ['mushroomc'], 'pic' => $gvar ['mushroomcpic'], 'type' => '0', 'comment' => $gvar ['mushroomcct'] );
$mushroomdlv = array ('id' => 'd', 'lv' => $gvar ['mushroomdlv'], 'point' => $gvar ['mushroomdp'], 'name' => $gvar ['mushroomd'], 'pic' => $gvar ['mushroomdpic'], 'type' => '0', 'comment' => $gvar ['mushroomdct'] );
$mushroomelv = array ('id' => 'e', 'lv' => $gvar ['mushroomelv'], 'point' => $gvar ['mushroomep'], 'name' => $gvar ['mushroome'], 'pic' => $gvar ['mushroomepic'], 'type' => '0', 'comment' => $gvar ['mushroomect'] );
$angellv = array ('id' => 'angel', 'lv' => $gvar ['angellv'], 'point' => $gvar ['angelp'], 'name' => $gvar ['angel'], 'pic' => $gvar ['angelpic'], 'type' => '0', 'comment' => $gvar ['angelct'] );
$demonlv = array ('id' => 'demon', 'lv' => $gvar ['demonlv'], 'point' => $gvar ['demonp'], 'name' => $gvar ['demon'], 'pic' => $gvar ['demonpic'], 'type' => '0', 'comment' => $gvar ['demonct'] );

if (trim ( $gvar ['visualkm'] ) == "") {
	$gvar ['visuallv'] = 0;
}

$visualkmlv = array ('id' => 'visualkm', 'lv' => $gvar ['visuallv'], 'point' => 0, 'name' => "visual", 'pic' => $gvar ['visualpic'], 'type' => '1', 'comment' => $gvar ['visualkmct'] );

$alllv = array ($mushroomalv, $mushroomblv, $mushroomclv, $mushroomdlv, $mushroomelv, $angellv, $demonlv, $visualkmlv );

//zlv
$numberweight = 0;
$tempdata = array ();
foreach ( $alllv as $one ) {
	$gweight += $one ['lv'];
	for($i = 0; $i < $one ['lv']; $i ++) {
		$tempdata [] = $one;
	}
}

$use = mt_rand ( 0, $gweight - 1 );
$one = $tempdata [$use];

$moneys = explode ( '~', $one ['point'] );

$moneyx = $moneys [0];
$moneys = $moneys [1];

$getmoney = mt_rand ( $moneyx, $moneys );

$message = $one ['comment'];

if ($one ['type'] == '0') {
	updatemembercount ( $_G ['uid'], array ('extcredits' . $gvar ['exttype'] => $getmoney ), true, '', 0, '' );
	$message = str_replace ( '{$jpname}', $one ['name'], $message );
	$message = str_replace ( '{$jppoint}', $getmoney, $message );
	$message = str_replace ( '{$jftype}', $credittype, $message );

} elseif ($one ['type'] == '1') {
	
	$kms = array_filter ( explode ( "\n", $gvar ['visualkm'] ) );
	foreach ( $kms as $k => $v ) {
		$v = trim ( $v );
		if ($v == '\r' || $v == '\n' || $v == '') {
			continue;
		}
		
		$zjkm = $v;
		$v = "******";
		$message = str_replace ( '{$jpname}', $v, $message );
		break;
	}
	
	foreach ( $kms as $k => $v ) {
		if ($zjkm != trim ( $v )) {
			$addkm .= $v . "\n";
		}
	}
	unset ( $data );
	$data ['value'] = $addkm;
	DB::update ( 'common_pluginvar', $data, "variable='visualkm' and displayorder='21'" );
	
	unset ( $data );
	$data ['uid'] = $_G ['uid'];
	$data ['username'] = $_G ['username'];
	$data ['message'] = str_replace ( "******", $zjkm, $message );
	$data ['create_time'] = TIMESTAMP;
	DB::insert ( 'genee_mush_km', $data );

}

if ($gvar [getcreditmessage]) {
	$notification = $message;
	notification_add ( $_G ['uid'], 'system', $notification, array (), 1 );
}
DB::query ( "UPDATE " . DB::table ( 'genee_mush_user' ) . " SET playnum=playnum-1 WHERE uid='$_G[uid]'" );
DB::query ( "UPDATE " . DB::table ( 'genee_mush_user' ) . " SET ljnum=ljnum+1 WHERE uid='$_G[uid]'" );

//dt
unset ( $data );
$data ['uid'] = $_G [uid];
$data ['username'] = $_G [username];
$data ['log'] = $message;
$data ['create_time'] = TIMESTAMP;
DB::insert ( 'genee_mush_log', $data );

//ph
unset ( $data );
$data ['uid'] = $_G [uid];
$data ['username'] = $_G [username];
$data ['credit'] = $getmoney;
$data ['create_time'] = TIMESTAMP;
DB::insert ( 'genee_mush_ph', $data );

$images = ($one ['pic'] ? $one ['pic'] : $gvar ['run']);
$return = $message . "&&&&" . $getmoney . "&&&&" . $images;
echo base64_encode ( $return );
exit ();

?>