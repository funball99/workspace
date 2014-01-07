<?php
require_once './abstractFollowUser.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../source/function/function_core.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../tool/constants.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
class followUserImpl_x25 extends abstractFollowUser {
	function getFollowUserObj() {
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
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		$followuid = intval($_GET['followId']);
		if(empty($followuid)) {
		
			$obj -> rs = FAILED;
			$obj -> errcode = "01000000";
			echo echo_json($obj);
			exit();
		}
		if($uid == $followuid) {
			echo '{"rs":0,"errcode":"01000000"}';exit();
		}
		$special = intval($_GET['special']) ? intval($_GET['special']) : 0;
		$followuser = getuserbyuid($followuid);
		$mutual = 0;
		$followed = C::t('home_follow')->fetch_by_uid_followuid($followuid, $uid);
		if(!empty($followed)) {
			if($followed['status'] == '-1') {
				showmessage('follow_other_unfollow');
			}
			$mutual = 1;
			C::t('home_follow')->update_by_uid_followuid($followuid, $uid, array('mutual'=>1));
		}
		$followed = C::t('home_follow')->fetch_by_uid_followuid($uid, $followuid);
		
		if(empty($followed)) {
			$user = getuserbyuid($uid);
			$followdata = array(
					'uid' => $uid,
					'username' => $user['username'],
					'followuid' => $followuid,
					'fusername' => $followuser['username'],
					'status' => 0,
					'mutual' => $mutual,
					'dateline' => TIMESTAMP
			);
			C::t('home_follow')->insert($followdata, false, true);
			C::t('common_member_count')->increase($uid, array('following' => 1));
			C::t('common_member_count')->increase($followuid, array('follower' => 1, 'newfollower' => 1));
			notification_add($followuid, 'follower', 'member_follow_add', array('count' => $count, 'from_id'=>$uid, 'from_idtype' => 'following'), 1);
		
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