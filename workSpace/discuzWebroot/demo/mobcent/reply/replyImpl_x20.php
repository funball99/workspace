<?php
require_once './abstractReply.php';
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../source/function/function_core.php';
require_once '../../source/function/function_post.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/constants.php';
require_once '../helper/helper_notification.php';
require_once '../../source/function/function_forumlist.php';
require_once '../model/table/x20/mobcentDatabase.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();


class replyImpl_x20 extends abstractReply {
	public function getReplyObj() {
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
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $_G ['uid'] =$arrAccess['user_id'];
		$tid = $_GET ['topicId']; 
		$subject = C::t('forum_thread')->get_subject_by_tid ( $tid );
		$message = $_POST ['rContent'] ? $_POST ['rContent'] : Common::get_unicode_charset('\u5185\u5bb9 '); 
		$message = str_replace("\\'", "&squot;", $message);
		$rPostion = empty($_GET ['r'])?0:1;
		$longitude = $_GET ['longitude']; 
		$latitude = $_GET ['latitude'];
		$location = echo_urldecode ( $_GET ['location'] );
		$subject = urldecode ( $subject );
		$message = str_replace("\\\"", "\"", $message);
		$message = str_replace('\\\"', "&dquot;", $message);
		$array_message = echo_array (urldecode ( $message ) );
		unset ( $message );
		$message = '';
		$val = $_GET ['aid'];
		$aid_Img = explode ( ',', $val );
		
		$i = 0;
		
		foreach ( $array_message as $k => $v ) {
			switch ($v ["type"]) {
				case 0 :
					$message .= $v ["infor"];
					file_put_contents('c5.txt', $message);
					break;
				case 1 :
					if (empty ( $aid_Img )) {
						$message .= '[attach]' . $val . '[/attach]';
					} else {
						$message .= '[attach]' . $aid_Img [$i] . '[/attach]';
						$i = $i + 1;
					}
					$attachment = 2;
					break;
			}
		}
		$fid = $_GET ['boardId']; 
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$_G['uid'] = $uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		$user = getuserbyuid ( $uid );
		
		$author = $username = $user ['username'];
		$ip = get_client_ip ();
		$pinvisible = 0;
		$usesig = 1;
		$htmlon = 0;
		$bbcodeoff = - 1;
		$smileyoff = - 1;
		$parseurloff = false;
		$isanonymous = $heatthreadset = $posttableid = $action = '';
		$dateline = time ();
		
		
		
		if ($_GET ['isQuote'] && $_GET ['toReplyId']) {
			$bbcodeoff = 0;
			$toReplyId = $_GET ['toReplyId'];
			$Reply = C::t('forum_thread')->get_forum_post_by_pid ( $toReplyId );
			$Reply ['message'] = preg_replace ( '#\[quote\][.\n\S\s]+\[/quote\]#', '', $Reply ['message'] );
			$message = '[quote][size=2][color=#999999]' . $Reply ['author'] . Common::get_unicode_charset('\u53d1\u8868\u4e8e') . date ( "Y-m-d H:i:s", $Reply ['dateline'] ) . '[/color] [url=forum.php?mod=redirect&goto=findpost&pid=' . $Reply ['pid'] . '&ptid=' . $Reply ['tid'] . '][img]static/image/common/back.gif[/img][/url][/size]' . CHR ( 10 ) . daddslashes($Reply ['message']) . CHR ( 10 ) . '[/quote]' . CHR ( 10 ) . $message;
		}
		
		$message = htmlspecialchars_decode ( $message );
		$message = str_replace( "&squot;", "\\'", $message);
		$message = str_replace( "&dquot;", '\\"', $message);
		$message = $_GET['platType'] ==1 ? $message."\r\n[url=/mobcent/download/down.php]".Common::get_unicode_charset('\u6765\u81ea\u5b89\u5353\u5ba2\u6237\u7aef').'[/url]':$message."\r\n[url=/mobcent/download/down.php]".Common::get_unicode_charset('\u6765\u81ea\u0069\u0070\u0068\u006f\u006e\u0065\u5ba2\u6237\u7aef')."[/url]";
		$pid = insertpost ( array (
				'fid' => $fid,
				'tid' => $tid,
				'first' => '0',
				'author' => $username,
				'authorid' => $uid,
				'subject' => '',
				'dateline' => $dateline,
				'message' => $message,
				'useip' => $ip,
				'invisible' => $pinvisible,
				'anonymous' => $isanonymous,
				'usesig' => $usesig,
				'htmlon' => $htmlon,
				'bbcodeoff' => 0,
				'smileyoff' => $smileyoff,
				'parseurloff' => $parseurloff,
				'attachment' => $attachment,
				'status' => 0
		) );
		
		$_G ['group'] ['allowat'] = substr_count ( $message, '@' );
		if ($_G ['group'] ['allowat']) {
			$bbcodeoff = 0; 
			preg_match_all ( "/@([^\r\n]*?)\s/i", $message . ' ', $atlist_tmp );
			$atlist_tmp = array_slice ( array_unique ( $atlist_tmp [1] ), 0, $_G ['group'] ['allowat'] );
			$atnum = $maxselect = 0;
			foreach($atlist_tmp as $key=>$user)
			{
				$userInfo = C::t('common_member')->getUserId($user);
				$note = array(
						'tid' => $tid,
						'subject' => $subject,
						'fid' => $_G['fid'],
						'pid' => $pid,
						'from_id' => $tid,
						'from_idtype' => 'at',
				);
				C::t('home_follow')->notification_add($userInfo['uid'], $uid,$username,'at', 'reppost_noticeauthor', $note);
				$maxselect = $_G ['group'] ['allowat'] - $atnum;
			}
		
		}
		$posttable = getposttablebytid($_G['tid']);
		$query = DB::query("SELECT tid, author, authorid, useip, dateline, anonymous, status, message FROM ".DB::table($posttable)." WHERE tid='$tid' AND first='1' AND invisible='0'");
		$thapost = DB::fetch($query);
		$note = array(
				'tid' => $tid,
				'subject' => $subject,
				'fid' => $_G['fid'],
				'pid' => $pid,
				'from_id' => $tid,
				'from_idtype' => 'post',
		);
		if($uid != $thapost['authorid'])
		{
			C::t('home_follow')->notification_add($thapost['authorid'], $uid,$username,'post', 'reppost_noticeauthor', $note);
		}
		
		$isgroup = $feedid = '';
		useractionlog ( $uid, 'pid' );
		
		$threadimageaid = $val;
		if (empty ( $aid_Img )) {
			if ($val) {
				$tableid = getattachtableid ( $tid );
				$attach = DB::fetch_first("SELECT * FROM ".DB::table('forum_attachment_unused')." WHERE aid='$val' AND uid='$uid'");
				$aids = $attach ['aid'];
				$data = $attach;
					
				$data ['uid'] = 1;
				$data ['tid'] = $tid;
				$data ['pid'] = $pid;
				DB::insert(getattachtablebytid($tid), $data, false, true);
				DB::update('forum_attachment', array('tid' => $tid, 'pid' => $pid, 'tableid' => getattachtableid($tid)), "aid='$aids'");
				DB::delete('forum_attachment_unused', "aid='$aids'");
			}
		} else {
			foreach ( $aid_Img as $key => $val ) {
				if ($val) {
					$tableid = getattachtableid ( $tid );
					$attach = DB::fetch_first("SELECT * FROM ".DB::table('forum_attachment_unused')." WHERE aid='$val' AND uid='$uid'");
					$aids = $attach ['aid'];
					$data = $attach;
		
					$data ['uid'] = 1;
					$data ['tid'] = $tid;
					$data ['pid'] = $pid;
					DB::insert(getattachtablebytid($tid), $data, false, true);
					DB::update('forum_attachment', array('tid' => $tid, 'pid' => $pid, 'tableid' => getattachtableid($tid)), "aid='$aids'");
					DB::delete('forum_attachment_unused', "aid='$aids'");
				}
			}
		}
		
		$lastpost = "$tid\t$subject\t$dateline\t$author";
		DB::query("UPDATE ".DB::table('forum_forum')." SET lastpost='$lastpost', posts=posts+1, todayposts=todayposts+1 WHERE fid='".$fid."'", 'UNBUFFERED');
		DB::query("UPDATE ".DB::table('forum_thread')." SET lastposter='$author', lastpost='$dateline', replies=replies+1 WHERE tid='".$tid."'", 'UNBUFFERED');
		
		$fieldarr = array (
				'lastposter' => array (
						$author
				),
				'replies' => 1
		);
		$replymessage = 'post_reply_succeed';
		
		$heatthreadset = update_threadpartake ( $tid, true );
		$updatethreaddata = $heatthreadset ? $heatthreadset : array ();
		$updatethreaddata [] = "'lastpost'='" . time () . "'";
		
		updatepostcredits ( '+', $uid, 'reply', $fid );
		if (isset ( $rPostion ) && ! empty ( $rPostion )) {
			C::t('home_surrounding_user')->insert_all_apply_location ( $longitude, $latitude, $location, $pid );
		}
		$data_post ["rs"] = 1;
		return $data_post;
		}

}

?>