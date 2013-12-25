<?php

/**
 * $Id: setting.inc.php 2012-03-15 10:42:10Z liudongdong $
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// ���԰�
$qqlang = $_G['cache']['pluginlanguage_script']['qqmedal'];

// �����б�
$creditlist = array();
foreach ($_G['setting']['extcredits'] as $key => $credit) {
	$creditlist[] = array($key, 'extcredits'.$key.' ('.$credit['title'].')');
}

// ����
$qqmedal = unserialize($_G['setting']['qqmedal']);

if (!submitcheck('submit')) {

	showformheader('plugins&operation=config&identifier=qqmedal&pmod=setting');
	showtableheader();
	showsetting($qqlang['allowed'], 'settingnew[allowed]', $qqmedal['allowed'], 'radio', '', 0, $qqlang['allowed_comment']);
    showtablerow('', 'class="td27" colspan="2"', $qqlang['show']);
    showtablerow('class="noborder"', '', '<img src="static/image/common/qqmedal.png">');
	showtablefooter();

	showtableheader();
	showsetting($qqlang['rewardcredit'], array('settingnew[rewardcredit]', $creditlist), $qqmedal['rewardcredit'], 'select', '', 0, $qqlang['rewardcredit_comment']);
	showsetting($qqlang['addcreditnum'], 'settingnew[addcreditnum]', $qqmedal['addcreditnum'], 'text', '', 0, $qqlang['addcreditnum_comment']);
	showsetting($qqlang['feed'], 'settingnew[feed]', $qqmedal['feed'], 'textarea', '', 0, $qqlang['feed_comment']);
	showtablefooter();

	showtableheader('', 'notop');
	showsubmit('submit');
	showtablefooter();
	showformfooter();
	exit;

} else {
	if ($_POST['settingnew']['allowed']) {
		// ��Ҫ�ȿ�����ƽ̨ QQ����
		$allowed = false;
		if ($_G['setting']['plugins']['available']) {
			// require_once libfile('function/cloud');
			// $allowed = in_array('qqconnect', $_G['setting']['plugins']['available']) && getcloudappstatus('connect');
			$appService = Cloud::loadClass('Service_App');
			$connectStatus = $appService->getCloudAppStatus('connect');
			$allowed = in_array('qqconnect', $_G['setting']['plugins']['available']) && $connectStatus;
		}

		if (!$allowed) {
			cpmsg('qqmedal:allowed_comment', '', 'error');
		}
	}

	$_POST['settingnew']['feed'] = trim(strip_tags($_POST['settingnew']['feed']));

	$_POST['settingnew']['rewardcredit'] = $_POST['settingnew']['rewardcredit'] ? intval(trim($_POST['settingnew']['rewardcredit'])) : '';
	$_POST['settingnew']['addcreditnum'] = $_POST['settingnew']['addcreditnum'] ? intval(trim($_POST['settingnew']['addcreditnum'])) : '';
	
	if (!$_POST['settingnew']['feed'] || !$_POST['settingnew']['rewardcredit'] || !$_POST['settingnew']['addcreditnum']) {
		cpmsg('qqmedal:credit_or_feed_empty', '', 'error');
	}
	
    $_POST['settingnew']['feed'] = cutstr($_POST['settingnew']['feed'], 140, '');
    
	$settingnew = array(
		'allowed' => intval(trim($_POST['settingnew']['allowed'])),
		'rewardcredit' => $_POST['settingnew']['rewardcredit'],
		'addcreditnum' => $_POST['settingnew']['addcreditnum'],
		'feed' => $_POST['settingnew']['feed'],
	);

	// $svalue = addslashes(serialize(dstripslashes($settingnew)));
	// DB::query("REPLACE INTO ".DB::table('common_setting')." (`skey`, `svalue`) VALUES ('qqmedal', '$svalue')");
	C::t('common_setting')->update_batch(array('qqmedal' => $settingnew));

	updatecache('setting');
	cpmsg('setting_update_succeed', 'action=plugins&operation=config&identifier=qqmedal&pmod=setting', 'succeed');
}

?>