<?php
require_once './abstractReply.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../source/function/function_core.php';
require_once '../../source/function/function_post.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/constants.php';
require_once '../model/table_forum_thread.php';
require_once '../model/table_surround_user.php';
require_once '../helper/helper_notification.php';
require_once '../public/mobcentDatabase.php';
require_once libfile ( 'function/forumlist' );
define('ALLOWGUEST', 1);
C::app ()->init ();   

class replyImpl_x25 extends abstractReply {
	public function getReplyObj() {
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
		$uid = $_G ['uid'] =$arrAccess['user_id'];
		$tid = $_GET ['topicId']; 
		$subject = get_subject_by_tid ( $tid );
		//$_GET['rContent']="%255B%257B%2522type%2522%253A0%252C%2522infor%2522%253A%2522yinyong%2522%257D%255D";
		$message = $_GET ['rContent'] ? $_GET ['rContent'] : Common::get_unicode_charset('\u5185\u5bb9 ');
		$message = urldecode ( $message );
		$rPostion = empty($_GET ['r'])?0:1;
		$longitude = $_GET ['longitude'];
		$latitude = $_GET ['latitude'];
		$location = echo_urldecode ( $_GET ['location'] );
		$subject = urldecode ( $subject );
		$array_message = echo_array ( urldecode ( $message ) );
		unset ( $message );
		$message = '';
		$val = $_GET ['aid'];
		$aid_Img = explode ( ',', $val );
		
		$i = 0;
		foreach ( $array_message as $k => $v ) {
			switch ($v ["type"]) {
				case 0 :
					$message .= $v ["infor"];
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
		$message = htmlspecialchars_decode ( $message );
		$_G ['group'] ['allowat'] = substr_count ( $message, '@' );
		$isQuote = empty($_GET['isQuote']) || $_GET['isQuote']=='false'?0:1;
		if ($_G ['group'] ['allowat']) {
			$bbcodeoff = 0; 
			$atlist = $atlist_tmp = array ();
			preg_match_all ( "/@([^\r\n]*?)\s/i", $message . ' ', $atlist_tmp );
			$atlist_tmp = array_slice ( array_unique ( $atlist_tmp [1] ), 0, $_G ['group'] ['allowat'] );
			$atnum = $maxselect = 0;
			foreach ( C::t ( 'home_notification' )->fetch_all_by_authorid_fromid ( $_G ['uid'], $_G ['tid'], 'at' ) as $row ) {
				$atnum ++;
				$ateduids [$row [uid]] = $row ['uid'];
			}
			$maxselect = $_G ['group'] ['allowat'] - $atnum;
			if (! empty ( $atlist_tmp )) {
				if (empty ( $_G ['setting'] ['at_anyone'] )) {
					foreach ( C::t ( 'home_follow' )->fetch_all_by_uid_fusername ( $uid, $atlist_tmp ) as $row ) {
						$atlist [$row ['followuid']] = $row ['fusername'];
					}
					if (count ( $atlist ) < $_G ['group'] ['allowat']) {
						$query = C::t ( 'home_friend' )->fetch_all_by_uid_username ( $uid, $atlist_tmp );
						foreach ( $query as $row ) {
							$atlist [$row ['fuid']] = $row ['fusername'];
						}
					}
				} else {
					foreach ( C::t ( 'common_member' )->fetch_all_by_username ( $atlist_tmp ) as $row ) {
						$atlist [$row ['uid']] = $row ['username'];
					}
				}
			}
			if ($atlist) {
					
				foreach ( $atlist as $atuid => $atusername ) {
					$atsearch [] = "/@$atusername /i";
					$atreplace [] = "[url=home.php?mod=space&uid=$atuid]@{$atusername}[/url] ";
				}
				$message = preg_replace ( $atsearch, $atreplace, $message . ' ', 1 );
			}
		}
		
		foreach ( C::t ( 'home_follow' )->fetch_all_by_uid_fusername ( $uid, $atlist_tmp ) as $row ) {
		
			if (! in_array ( $row ['followuid'], $ateduids )) {
				$atlist [$row [followuid]] = $row ['fusername'];
			}
			if (count ( $atlist ) == $maxselect) {
				break;
			}
		}
		if (count ( $atlist ) <= $maxselect) {
			$query = C::t ( 'home_friend' )->fetch_all_by_uid_username ( $uid, $atlist_tmp );
			foreach ( $query as $row ) {
				if (! in_array ( $row ['followuid'], $ateduids )) {
					$atlist [$row [fuid]] = $row ['fusername'];
				}
			}
		}
		if ($isQuote==1 && $_GET ['toReplyId']) {
			$bbcodeoff = 0;
			$toReplyId = $_GET ['toReplyId'];
			$Reply = get_forum_post_by_pid ( $toReplyId );
			if (1) {
			}
			$Reply ['message'] = preg_replace ( '#\[quote\][.\n\S\s]+\[/quote\]#', '', $Reply ['message'] );
			$message = '[quote][size=2][color=#999999]' . $Reply ['author'] . Common::get_unicode_charset('\u53d1\u8868\u4e8e') . date ( "Y-m-d H:i:s", $Reply ['dateline'] ) . '[/color] [url=forum.php?mod=redirect&goto=findpost&pid=' . $Reply ['pid'] . '&ptid=' . $Reply ['tid'] . '][img]static/image/common/back.gif[/img][/url][/size]' . CHR ( 10 ) . $Reply ['message'] . CHR ( 10 ) . '[/quote]' . CHR ( 10 ) . $message;
		}
		$thread = C::t ( 'forum_thread' )->fetch ( $tid );
		$message = $_GET['platType'] ==1 ? $message."\r\n[url=/mobcent/download/down.php]".Common::get_unicode_charset('\u6765\u81ea\u5b89\u5353\u5ba2\u6237\u7aef').'[/url]':$message."\r\n[url=/mobcent/download/down.php]".Common::get_unicode_charset('\u6765\u81ea\u0069\u0070\u0068\u006f\u006e\u0065\u5ba2\u6237\u7aef')."[/url]";
		$pid = insertpost ( array (
				'fid' => $fid,
				'tid' => $tid,
				'first' => '0',
				'author' => $username,
				'authorid' => $uid,
				'subject' => '',
				'dateline' => $dateline,
				'message' => str_replace('\r\n', '\\r\\n', $message),
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
		
		
		$thapost = C::t ( 'forum_post' )->fetch ( 'tid:' . $tid, $pid );
		if($isQuote==0 && $uid != $thread['authorid']){ 
			mobcent_helper_notification::notification_add ( $username, $thread ['authorid'], 'post', 'reppost_noticeauthor',$uid, array (
			'tid' => $thread ['tid'],
			'subject' => $thread ['subject'],
			'fid' => $thread ['id'],
			'pid' => $pid,
			'from_id' =>$thread ['tid'],
			'from_idtype' => 'post'
			) );
		}else{ 
			if ($uid != $Reply ['authorid']) {
				mobcent_helper_notification::notification_add ( $username, $Reply ['authorid'], 'post', 'reppost_noticeauthor',$uid, array (
				'tid' => $Reply ['tid'],
				'subject' => $Reply ['subject'],
				'fid' => $Reply ['id'],
				'pid' => $pid,
				'from_id' =>  $Reply ['pid'],
				'from_idtype' => 'quote'
				));
			}
		}
		$isgroup = $feedid = '';
		useractionlog ( $uid, 'pid' );
		C::t ( 'common_stat' )->updatestat ( $uid, 'post' );
		
		if ($_G ['group'] ['allowat'] && $atlist) {
			foreach ( $atlist as $atuid => $atusername ) {
				$array1 = array (
						'from_id' => $tid,
						'from_idtype' => 'at',
						'buyerid' => $uid,
						'buyer' => $author,
						'tid' => $tid,
						'subject' => $subject,
						'pid' => $pid,
						'message' => messagecutstr ( $message, 150 )
				);
				mobcent_helper_notification::notification_add ( $username, $atuid, 'at', 'at_message',$uid, $array1 );
			}
			set_atlist_cookie ( array_keys ( $atlist ) );
		}
		
		$threadimageaid = $val;
		if (empty ( $aid_Img )) {
			if ($val) {
				$query = get_forum_attachment_unused ( $val );
				while ( $attach = DB::fetch ( $query ) ) {
					$aids = $attach ['aid'];
					$data = $attach;
				}
				$attachtableid = getattachtableid ( $tid );
				update_forum_attachment ( $tid, $attachtableid, $uid, $pid, $aids );
				$data ['uid'] = 1;
				$data ['tid'] = $tid;
				$data ['pid'] = $pid;
				echo 'hhh';exit;
				C::t ( 'forum_attachment_n' )->insert ( $tableid, $data );
			}
		} else {
			foreach ( $aid_Img as $key => $val ) {
				if ($val) {
					$tableid = getattachtableid ( $tid );
					$query = get_forum_attachment_unused ( $val );
					while ( $attach = DB::fetch ( $query ) ) {
						$aids = $attach ['aid'];
						$data = $attach;
					}
					DB::query ( "UPDATE %t SET tid=%d,tableid=%d,uid=%d,pid=%d WHERE aid IN (%n)", array (
					'forum_attachment',
					$tid,
					getattachtableid ( $tid ),
					$uid,
					$pid,
					$aids
					) );
					$data ['uid'] = 1;
					$data ['tid'] = $tid;
					$data ['pid'] = $pid;
					C::t ( 'forum_attachment_n' )->insert ( $tableid, $data );
				}
			}
		}
		
		$lastpost = "$tid\t$subject\t$dateline\t$author";
		C::t ( 'forum_forum' )->update ( $fid, array (
		'lastpost' => $lastpost
		) );
		C::t ( 'forum_forum' )->update_forum_counter ( $fid, 0, 1, 1 );
		
		$fieldarr = array (
				'lastposter' => array (
						$author
				),
				'replies' => 1
		);
		$replymessage = 'post_reply_succeed';
		
		
		$heatthreadset = update_threadpartake ( $tid, true );
		$updatethreaddata = $heatthreadset ? $heatthreadset : array ();
		$postionid = C::t ( 'forum_post' )->fetch_maxposition_by_tid ( $posttableid, $tid );
		$updatethreaddata [] = DB::field ( 'maxposition', $postionid );
		$updatethreaddata = array_merge ( $updatethreaddata, C::t ( 'forum_thread' )->increase ( $tid, $fieldarr, false, 0, true ) );
		$updatethreaddata [] = "'lastpost'='" . time () . "'";
		update_thread_pos ( $postionid, $author, $tid );
		
		updatepostcredits ( '+', $uid, 'reply', $fid );
		if (isset ( $rPostion ) && ! empty ( $rPostion )) {
			surround_user::insert_all_apply_location ( $longitude, $latitude, $location, $pid );
		}
		$data_post ["rs"] = 1;
		return $data_post;
			}
		}

?>