<?php
require_once './abstractReportUser.php';
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../Config/public.php';
require_once '../tool/tool.php';
require_once '../public/common_json.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
define('IN_MOBCENT',1);
require_once '../model/table/x20/mobcentDatabase.php';

class postReportUserImpl_x20 extends abstractReportUser {
	public function getReportUserObj() {
		$info = new mobcentGetInfo();
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
		$group = $info->rank_check_allow($accessSecret,$accessToken,$qquser);
		if(!$group['allowvisit'])
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '01110001';
			return $data_post;
			exit();
		}
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$userId = $arrAccess['user_id'];
		if(empty($userId))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		$space = $info->getUserInfo(intval($userId));
		$_G=array_merge($_G,$space);
		$_GET['message'] = iconv('utf-8','gb2312',urldecode($_GET['reason']));
		switch($_GET['type']){
			case 1: $rtype = 'thread';  
				break;
			case 2: $rtype = 'post';  
				break;
			case 3: $rtype = 'user'; 
				break;
		}
		$rid = intval($_GET['oid']);	 
		$tid = intval($_GET['tid']);	 
		$fid = intval($_GET['boardId']);	 
		$uid = intval($_GET['uid']);	 
		$default_url = array(
			'user' => 'home.php?mod=space&uid=',
			'post' => 'forum.php?mod=redirect&goto=findpost&ptid='.$tid.'&pid=',
			'thread' => 'forum.php?mod=viewthread&tid=',
			'group' => 'forum.php?mod=group&fid=',
			'album' => 'home.php?mod=space&do=album&uid='.$uid.'&id=',
			'blog' => 'home.php?mod=space&do=blog&uid='.$uid.'&id=',
			'pic' => 'home.php?mod=space&do=album&uid='.$uid.'&picid='
		);
		$url = '';
		if($rid && !empty($default_url[$rtype])) {
			$url = $default_url[$rtype].intval($rid);
		} else {
			$url = addslashes(dhtmlspecialchars(base64_decode($_GET['url'])));
			$url = preg_match("/^http[s]?:\/\/[^\[\"']+$/i", trim($url)) ? trim($url) : '';
		}
		
		$urlkey = md5($url);
		
			$message = censor(cutstr(dhtmlspecialchars(trim($_GET['message'])), 200, ''));
			$message = $_G['username'].'&nbsp;:&nbsp;'.rtrim($message, "\\");
			$reportid = C::t('common_report')->fetch_by_urlkey($urlkey,$userId);
			if((int)$reportid && isset($reportid)) {
                $data_notice['rs'] = 0;
                $data_notice['errcode'] = '01140101';
			} else {
				$data = array('url' => $url, 'urlkey' => $urlkey, 'uid' => $userId, 'username' => $space['username'], 'message' => $message, 'dateline' => TIMESTAMP);
				if($fid) {
					$data['fid'] = $fid;
				}
				C::t('common_report')->insert($data);
				$report_receive = unserialize($_G['setting']['report_receive']);
				$moderators = array();
				if($report_receive['adminuser']) {
					foreach($report_receive['adminuser'] as $touid) {
						notification_add($touid, 'report', 'new_report', array('from_id' => 1, 'from_idtype' => 'newreport'), 1);
					}
				}
				if($fid && $rtype == 'post') {
					foreach(C::t('forum_moderator')->fetch_all_by_fid($fid, false) as $row) {
						$moderators[] = $row['uid'];
					}
					if($report_receive['supmoderator']) {
						$moderators = array_unique(array_merge($moderators, $report_receive['supmoderator']));
					}
					foreach($moderators as $touid) {
						$touid != $_G['uid'] && !in_array($touid, $report_receive) && notification_add($touid, 'report', 'new_post_report', array('fid' => $fid, 'from_id' => 1, 'from_idtype' => 'newreport'), 1);
					}
				}
				$data_notice['rs'] = 1;
			}
				return $data_notice;
		}

}

?>