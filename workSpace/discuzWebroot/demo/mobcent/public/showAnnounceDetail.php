<?php
 
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../tool/tool.php';
C::app ()->init ();
require_once '../tool/constants.php';
require_once libfile ( 'function/discuzcode' );
$announcedata = C::t ( 'forum_announcement' )->fetch_all_by_date ( $_G ['timestamp'] );
if (! count ( $announcedata )) {
	showmessage ( 'announcement_nonexistence' );
}

$announcelist = array ();
foreach ( $announcedata as $announce ) {
	$announce ['authorenc'] = rawurlencode ( $announce ['author'] );
	$tmp = explode ( '.', dgmdate ( $announce ['starttime'], 'Y.m' ) );
	$months [$tmp [0] . $tmp [1]] = $tmp;
	if (! empty ( $_GET ['m'] ) && $_GET ['m'] != dgmdate ( $announce ['starttime'], 'Ym' )) {
		continue;
	}
	$announce ['starttime'] = dgmdate ( $announce ['starttime'], 'd' );
	$announce ['endtime'] = $announce ['endtime'] ? dgmdate ( $announce ['endtime'], 'd' ) : '';
	$announce ['message'] = $announce ['type'] == 1 ? "[url]{$announce[message]}[/url]" : $announce ['message'];
	$announce ['message'] = nl2br ( discuzcode ( $announce ['message'], 0, 0, 1, 1, 1, 1, 1 ) );
	$announcelist [] = $announce;
}
$annid = isset ( $_GET ['announceId'] ) ? intval ( $_GET ['announceId'] ) : 0;
foreach ( $announcelist as $k => $announce ) {
	$data ['announce_content'] [] = array (
			'infor' => $announce ["message"],
			'type' => 0 
	);
	$data ['announce_id'] = ( int ) $announce ["id"]; 
	$data ['author'] = $announce ["author"];  
	$data ['title'] = $announce ["subject"];  
	$data ['start_date'] = $announce ["starttime"] . '000'; 
	$member = DB::fetch_first('SELECT uid FROM %t WHERE username=%s', array('common_member', $announce ["author"]));
	$data ['icon']		=userIconImg($member['uid']);
	$data_anns ['announce_detail'] = $data;
}

if (1) {
	$data_anns ['img_url'] = '';
	$data_anns ['icon_url'] = DISCUZSERVERURL;
	$data_anns ['rs'] = '1';
	
	echo echo_json ( $data_anns );
	exit ();
} else {
	$obj -> rs = SUCCESS;
	echo echo_json($obj);
}

?>