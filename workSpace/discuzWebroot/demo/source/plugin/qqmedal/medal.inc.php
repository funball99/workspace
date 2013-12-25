<?php

/**
 * $Id: medal.inc.php 2012-03-15 10:42:10Z liudongdong $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
//error_reporting(E_ALL);
$qqmedal = unserialize($_G['setting']['qqmedal']);


if (!$_G['setting']['connect']['allow'] || $_G['cookie']['has_qqmedal'] == 1 || !$_G['cookie']['client_created'] || (time() - $_G['cookie']['client_created'] > 60) && $_G['cookie']['client_created'] || !$_G['uid'] || !$_G['member']['conisbind']  || !$qqmedal['allowed']) {
    return;
}

$cookie_expires = 2592000;

$mid = $_G['setting']['qqmedalid'];
if (!$mid) {
	return;
}

// �ж�ѫ���Ƿ����
// $available = DB::result_first("SELECT available FROM " . DB::table('forum_medal') . " WHERE medalid = $mid");
$medal = C::t('forum_medal')->fetch_all_by_id($mid);
$available = $medal[0]['available'];
if (!$available) {
	return;
}

// require_once libfile('function/connect');
// connect_merge_member();
$connectService = Cloud::loadClass('Service_Connect');
$connectService->connectMergeMember();

// $medals = DB::result_first("SELECT medals FROM " . DB::table('common_member_field_forum') . " WHERE uid = $_G[uid]");

// if ($medals && in_array($mid, explode("\t", $medals))) {
	// // ����ѷ�
	// dsetcookie('has_qqmedal', 1, $cookie_expires);
	// return;
// }
if(C::t('common_member_medal')->count_by_uid_medalid($_G['uid'], $mid)) {
	// ����ѷ�
	dsetcookie('has_qqmedal', 1, $cookie_expires);
	exit;
}

$rewardcredit = $qqmedal['rewardcredit'] ? intval($qqmedal['rewardcredit']) : 1; //Ĭ��credits1
$addcreditnum = $qqmedal['addcreditnum'] ? intval($qqmedal['addcreditnum']) : 2; //Ĭ�ϼ�2

if (submitcheck('publishsubmit')) {

	$_G['publish_feed'] = $_POST['connect_publish_feed_infloat'] == 0 ? 0 : 1; // ���͵�Qzone��Ĭ������
	$_G['publish_t'] = $_POST['connect_publish_t_infloat'] == 0 ? 0 : 1; // ���͵�΢����Ĭ������
	
	if ($_G['publish_feed'] || $_G['publish_t']) {

		// �ӻ���
		if ($rewardcredit && $addcreditnum) {
			updatemembercount($_G['uid'], array($rewardcredit => $addcreditnum), 1);
		}

		$connectOAuthClient = Cloud::loadClass('Service_Client_ConnectOAuth');
		$url = $_G['siteurl'];

		if ($_G['publish_feed']) {
			$qzone_params = array(
				'title' => lang('plugin/qqmedal', 'qqmedal') . lang('plugin/qqmedal', 'title'),
				'summary' => $qqmedal['feed'],
				'url' => $url.'?ADTAG=DISCUZ.QQMEDAL.QZONE',
				'nswb' => '1', // ���Զ�ͬ����΢��
			);

			try {
				$response = $connectOAuthClient->connectAddShare($_G['member']['conopenid'], $_G['member']['conuin'], $_G['member']['conuinsecret'], $qzone_params);
			} catch(Exception $e) {
				$errorCode = $e->getCode();
			}
		}

		if ($_G['publish_t']) {
			$t_params = array(
				'content' => lang('plugin/qqmedal', 'qqmedal') . $qqmedal['feed'] . $url . '?ADTAG=DISCUZ.QQMEDAL.WEIBO',
			);
			try {
				$response = $connectOAuthClient->connectAddT($_G['member']['conopenid'], $_G['member']['conuin'], $_G['member']['conuinsecret'], $t_params);
			} catch(Exception $e) {
				$errorCode = $e->getCode();
			}
		}

	}

	// ����ѫ��
	$result = C::t('common_member_field_forum')->fetch($_G['uid']);
	$medals = $result['medals'];
	$medalsnew = $medals ? $mid . "\t" . $medals : $mid;

	// DB::query("UPDATE ".DB::table('common_member_field_forum')." SET medals='$medalsnew' WHERE uid='$_G[uid]'", 'UNBUFFERED');
	C::t('common_member_field_forum')->update($_G['uid'], array('medals' => $medalsnew));
	C::t('common_member_medal')->insert(array('uid' => $_G['uid'], 'medalid' => $mid), 0, 1);
	
	// DB::query("INSERT INTO ".DB::table('forum_medallog')." (uid, medalid, type, dateline, expiration, status) VALUES ('$_G[uid]', '$mid', '0', '$_G[timestamp]', '', '1')");
	C::t('forum_medallog')->insert(array(
		'uid' => $_G['uid'],
		'medalid' => $mid,
		'type' => 0,
		'dateline' => TIMESTAMP,
		'expiration' => '',
		'status' => 0,
	));
	
	// ����ѷ�
	dsetcookie('has_qqmedal', 1, $cookie_expires);

	$message = lang('plugin/qqmedal', 'publish_succeed');
	
	include template('common/header');
	$return  = <<<EOF
<script type="text/javascript" reload="1">

	var message = "{$message}";
	var dialog_id = "{$_POST['handlekey']}";
	hideWindow(dialog_id);

	showDialog(message, 'right', null, null, 0);
</script>
EOF;
	echo $return;
	include template('common/footer');

} else {
	// ��Ǵ���״̬
	dsetcookie('has_qqmedal', 2);
	include template('qqmedal:medal');
}

?>