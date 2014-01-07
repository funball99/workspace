<?php
require_once './abstarctMsgReply.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../tool/constants.php';
require_once '../Config/public.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();

class msgReplyImpl_x25 extends abstarctMsgReply {
	public function getmsgReplyObj() {
		$mod = 'spacecp';
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
		$_G ['uid'] = $start_id =$arrAccess['user_id'];
		if(empty($_G ['uid']))
		{
			return $info -> userAccessError();
			exit();
		}
		$_POST ['message'] =echo_urldecode($_GET ['content']);
		$uid = empty ( $_GET ['toUserId'] ) ? 3 : intval ( $_GET ['toUserId'] );  
		$error = '';
		$pmid = empty ( $_GET ['pmid'] ) ? 0 : floatval ( $_GET ['pmid'] );
		$plid = empty ( $_GET ['plid'] ) ? 0 : intval ( $_GET ['plid'] );
		$opactives ['pm'] = 'class="a"';
		if ($uid) {
			$touid = $uid;
		} else {
			$touid = empty ( $_GET ['touid'] ) ? 0 : intval ( $_GET ['touid'] );
		}
		$daterange = empty ( $_GET ['daterange'] ) ? 1 : intval ( $_GET ['daterange'] );
		
		if (! empty ( $_POST ['username'] )) {
			$_POST ['users'] [] = $_POST ['username'];
		}
		$users = empty ( $_POST ['users'] ) ? array () : $_POST ['users'];
		$type = intval ( $_POST ['type'] );
		$coef = 1;
		if (! empty ( $users )) {
			$coef = count ( $users );
		}
		
		! ($_G ['group'] ['exempt'] & 1) && checklowerlimit ( 'sendpm', 0, $coef );
		
		$message = (! empty ( $_POST ['messageappend'] ) ? $_POST ['messageappend'] . "\n" : '') . trim ( $_POST ['message'] );
		if (empty ( $message )) {
			$error = '02000022';
		}
		$message = censor ( $message );
		loadcache ( array (
		'smilies',
		'smileytypes'
		) );
		foreach ( $_G ['cache'] ['smilies'] ['replacearray'] as $key => $smiley ) {
			$_G ['cache'] ['smilies'] ['replacearray'] [$key] = '[img]' . $_G ['siteurl'] . 'static/image/smiley/' . $_G ['cache'] ['smileytypes'] [$_G ['cache'] ['smilies'] ['typearray'] [$key]] ['directory'] . '/' . $smiley . '[/img]';
		}
		$subject = 'luanma';
		if ($type == 1) {
			$subject = dhtmlspecialchars ( trim ( $subject ) );
		}
		
		include_once libfile ( 'function/friend' );
		$return = 0;
		if ($touid || $pmid) {
			if ($touid) {
				$blackfriend = DB::fetch_first ( 'SELECT uid FROM %t WHERE  buid=%d limit 1', array (
						'home_blacklist',
						$start_id,
				) );
				if ($blackfriend ['uid'] == $uid) {
					$is_black = 1;
				}
				if (($value = getuserbyuid ( $touid ))) {
					$value ['onlyacceptfriendpm'] = $value ['onlyacceptfriendpm'] ? $value ['onlyacceptfriendpm'] : ($_G ['setting'] ['onlyacceptfriendpm'] ? 1 : 2);
					if ($_G ['group'] ['allowsendallpm'] || ($value ['onlyacceptfriendpm'] == 2 && !$is_black)|| ($value ['onlyacceptfriendpm'] == 1 && friend_check ( $touid ))) {
						$return = sendpm ( $touid, $subject, $message, $start_id, 0, 0, $type );
					} else {
						$error = '02000020';
					}
				} else {
					$error = '02000021';
				}
			} else {
				$return = sendpm ( 0, $subject, $message, $start_id, $pmid, 0 );
			}
		} elseif ($users) {
			$newusers = $uidsarr = $membersarr = array ();
			if ($users) {
				$membersarr = C::t ( 'common_member' )->fetch_all_by_username ( $users );
				foreach ( $membersarr as $aUsername => $aUser ) {
					$uidsarr [] = $aUser ['uid'];
				}
			}
			if (empty ( $membersarr )) {
				$error = '02000018';
			}
			if (isset ( $membersarr [$_G ['uid']] )) {
				$error = '02000019';
			}
		
			friend_check ( $uidsarr );
		
			foreach ( $membersarr as $key => $value ) {
		
				$value ['onlyacceptfriendpm'] = $value ['onlyacceptfriendpm'] ? $value ['onlyacceptfriendpm'] : ($_G ['setting'] ['onlyacceptfriendpm'] ? 1 : 2);
				if ($_G ['group'] ['allowsendallpm'] || $value ['onlyacceptfriendpm'] == 2 || ($value ['onlyacceptfriendpm'] == 1 && $_G ['home_friend_' . $value ['uid'] . '_' . $_G ['uid']])) {
					$newusers [$value ['uid']] = $value ['username'];
					unset ( $users [array_search ( $value ['username'], $users )] );
				}
			}
		
			if (empty ( $newusers )) {
				$error = '02000017';
			}
		
			foreach ( $newusers as $key => $value ) {
			}
			$coef = count ( $newusers );
			$return = sendpm ( implode ( ',', $newusers ), $subject, $message, $start_id, 0, 1, $type );
		} else {
			$return = - 9;
		}
		
		if ($return > 0) {
			include_once libfile ( 'function/stat' );
			updatestat ( 'sendpm', 0, $coef );
			C::t ( 'common_member_status' )->update ( $_G ['uid'], array (
			'lastpost' => TIMESTAMP
			) );
			! ($_G ['group'] ['exempt'] & 1) && updatecreditbyaction ( 'sendpm', 0, array (), '', $coef );
			$data['rs'] = 1;  
			$data['sendTime'] = time().'000';
			$data['pmid'] = $return;
		
			return $data;
			exit ();
		} else {
			switch (abs ( $return )) {
				case 1 :
					$error = '02000001';
					break;
				case 2 :
					$error = '02000002';
					break;
				case 3 :
					$error = '02000003';
					break;
				case 4 :
					$error = '02000004';
					break;
				case 5 :
					$error = '02000005';
					break;
				case 6 :
					$error = '02000006';
					break;
				case 7 :
					$error = '02000007';
					break;
				case 8 :
					$error = '02000008';
					break;
				case 9 :
					$error = '02000009';
					break;
				case 10 :
					$error = '02000010';
					break;
				case 11 :
					$error = '02000011';
					break;
				case 12 :
					$error = '02000012';
					break;
				case 13 :
					$error = '02000013';
					break;
				case 14 :
					$error = '02000014';
					break;
				case 15 :
					$error = '02000015';
					break;
				case 16 :
					$error = '02000016';
					break;
					default:
			    	$error = '01080015';
			    	break;
			}
		}
		if ($error) {
			$data['rs'] = 0;
			$data['errcode'] = $error;
			return $data; 
		}
		return $data;
		}
}

?>