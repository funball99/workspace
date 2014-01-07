<?php

/**
 * $Id: install.php 2012-03-15 10:42:10Z liudongdong $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$finish = false;

$data = array(
            'displayorder' => '-1',
            'name' => $installlang['qqmedal'],
            'available' => 1,
            'image' => 'qqmedal.png',
            'type' => 0,
            'description' => $installlang['qqmedal_comment']
        );

// $qqmedalid = DB::insert('forum_medal', $data, 1);
$qqmedalid = C::t('forum_medal')->insert($data, 1);

if ($qqmedalid) {
	// DB::query("REPLACE INTO ".DB::table('common_setting')." VALUES ('qqmedalid', $qqmedalid)");
	C::t('common_setting')->update_batch(array('qqmedalid' => $qqmedalid));
    
    foreach ($_G['setting']['extcredits'] as $key => $credit) {
        $creditlist[] = $key;
    }
    
    // 需要先开启云平台 QQ互联
    $allowed = false;
    if ($_G['setting']['plugins']['available']) {
        //require_once libfile('function/cloud');
		// $allowed = in_array('qqconnect', $_G['setting']['plugins']['available']) && getcloudappstatus('connect');
        $appService = Cloud::loadClass('Service_App');
		$connectStatus = $appService->getCloudAppStatus('connect');
        $allowed = in_array('qqconnect', $_G['setting']['plugins']['available']) && $connectStatus;
    }
    
    $settingnew = array(
		'allowed' => $allowed ? 1 : 0,
		'rewardcredit' => $creditlist[0],
		'addcreditnum' => 10,
		'feed' => $installlang['publishfeed'],
	);
	
	// $svalue = addslashes(serialize(dstripslashes($settingnew)));
	// DB::query("REPLACE INTO ".DB::table('common_setting')." (`skey`, `svalue`) VALUES ('qqmedal', '$svalue')");
	C::t('common_setting')->update_batch(array('qqmedal' => $settingnew));
    
    updatecache(array('setting', 'medals'));

    if (!$allowed) {
        cpmsg($installlang['tips'], 'action=plugins', 'succeed');
    }
	$finish = true;
}

?>