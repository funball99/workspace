<?php
require_once './abstractFollowUser.php';
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../source/function/function_core.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../tool/constants.php';
require_once '../public/common_json.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../uc_client/client.php';
require_once ('./abstractFollowUser.php');
require_once '../model/table/x20/mobcentDatabase.php';
class followUserImpl_x20 extends abstractFollowUser {
	function getFollowUserObj() {
		$info = new mobcentGetInfo ();
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
		$_G['uid'] = $uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		$followuid = intval($_GET['followId']);
		if(empty($followuid)) {
		
			$obj -> rs = 0;
			$obj -> errcode = "01000000";
			echo echo_json($obj);
			exit();
		}
		if($uid == $followuid) {
			$obj -> rs = 0;
			$obj -> errcode = "01000000";
			echo echo_json($obj);
			exit();
		}
		$special = intval($_GET['special']) ? intval($_GET['special']) : 0;
		$followuser = getuserbyuid($followuid);
		$mutual = 0;
		$followed = C::t('home_follow')->fetch_by_uid_followuid($uid, $followuid);
		if(!empty($followed)) {
			if($followed['status'] == '-1') {
				showmessage('follow_other_unfollow');
			}
			$mutual = 1;
			$followdata = array(
					'uid' => $uid,
					'fusername' => $followuser['username'],
					'fuid' => $followuid,
					'gid' => 1,
					'dateline' => TIMESTAMP
			);
			C::t('home_follow')->insert($followdata, false, true);
		}
		$followed = C::t('home_follow')->fetch_by_uid_followuid($uid, $followuid);
		
		if(empty($followed)) {
			$user = getuserbyuid($uid);
			$followdata = array(
					'uid' => $uid,
					'fusername' => $user['username'],
					'fuid' => $followuid,
					'fusername' => $followuser['username'],
					'dateline' => TIMESTAMP
			);
			C::t('home_follow')->insert($followdata, false, true);
			C::t('common_member_count')->increase($uid);
			$note = array(
					$_POST['note'] = '',
					'uid' => $followuid,
					'url' => 'home.php?mod=spacecp&ac=friend&op=add&uid='.$followuid.'&from=notice',
					'from_id' => $followuid,
					'from_idtype' => 'friendrequest',
					'note' => !empty($_POST['note']) ? lang('spacecp', 'friend_request_note', array('note' => $_POST['note'])) : ''
			);
			
			C::t('home_follow')->notification_add($followuid, $uid,$user['username'],'friend', 'friend_request', $note);
		
		} elseif($special) {
			$status = $special == 1 ? 1 : 0;
			C::t('home_follow')->update_by_uid_followuid($uid, $followuid, array('status'=>$status));
			$special = $special == 1 ? 2 : 1;
		}
		$data['rs'] = (Int)1;
		return $data;
		}

}

?>