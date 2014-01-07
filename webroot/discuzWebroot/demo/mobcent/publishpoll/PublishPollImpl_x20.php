<?php
require_once './abstractPublishPoll.php';
define ( 'IN_MOBCENT', 1 );
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/constants.php';

require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'class/credit' );
require_once libfile ( 'function/post' );
require_once libfile ( 'function/forum' );
require_once '../model/table/x20/mobcentDatabase.php';

class PublishPollImpl_x20 extends abstractPublishPoll {
	public function getPublishPollObj() {
		$info = new mobcentGetInfo ();
		$rPostion = $_GET['r'] ? $_GET['r']:0;   
		$longitude =$_GET['longitude']; 
		$latitude =	$_GET['latitude'];	 
		$location	=	echo_urldecode($_GET['location']);	 
		$aid = $_REQUEST ['aid'];   
		$aid_Img=explode(',',$aid);
		$readperm = 0;
		$price = 0;
		$typeid = 0;
		$sortid = 0;
		$displayorder = 0;
		$digest = 0;
		$special = 1;
		$attachment = 0;
		$moderated = 0;
		$thread ['status'] = 0;
		$isgroup = 0;
		$replycredit = 0;
		$closed = 0;
		$publishdate = time ();
		$fid = $_G ['fid'] = $_GET ['boardId'];
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
		$uid = $_G ['uid'] = $arrAccess['user_id'];
		$userInfoId = getuserbyuid ( $uid );
		
		$author = $username = $userInfoId ['username'];
		require_once '../model/table/x20/mobcentDatabase.php';
		$info = new mobcentGetInfo ();
		$modnewposts = $info ->getBoard($_G ['fid']);
		$displayorder = $modnewposts['modnewposts'] > 0?-2:0;
		$space = $info->getUserInfo ( intval ( $uid ) );
		if(empty($_G ['uid']))
		{
			return C::t('common_member') -> userAccessError();
			exit();
			
		}
		if(empty($space) || !$space)
		{
			$data_post ["rs"] = 0;
			$data_post ["error"] = '01010005';
			return $data_post;
			exit();
		}
		$author = $space ['username'];
		$_G ['username'] = $lastposter = $author;
		$_G = array_merge ( $_G, $space );
		
		
		/*renxing vote data and check */
		$pollItem=echo_array(urldecode($_GET['pollItem']));
		$pollarray=array();
		foreach($pollItem as $poll){
			$pitems[]=$poll[itemName];
		}
		if(count($pitems)>20)
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000020';
			return $data_post;
			exit();
		}
		if(count($pitems)<2)
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000021';
			return $data_post;
			exit();
		}
		if(!(preg_match("/^\d*$/", trim($_GET['type'])))) {
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000022';
			return $data_post;
			exit();
		}
		if(!(preg_match("/^\d*$/", trim($_GET['deadline'])))) {
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000023';
			return $data_post;
			exit();
		}

		$pollarray[maxchoices]=empty($_GET['type']) ? 0 : $_GET['type'];
		$pollarray[multiple]=(empty($_GET['type'])||intval($_GET['type'])==1)?0:1;
		$pollarray[options]=$pitems;
		$pollarray[visible] = empty($_GET['isVisible']);
		$pollarray[overt] = !empty($_GET['overt']);
		if(empty($_GET['deadline'])) {
			$pollarray['expiration'] = 0;
		} else {
			$pollarray['expiration'] = TIMESTAMP + 86400 * $_GET['deadline'];
		}
		/*end check */
	 
		$message = $_GET ['content'];
		$subject = echo_urldecode ( $_GET ['title'] ) ;
		$array_message = echo_array(urldecode($message));
		unset ( $message );
		$message = '';
		$i=0;
		foreach ( $array_message as $k => $v ) {
			switch ($v ["type"]) {
				case 0 :
					$message .= $v ["infor"];
					break;
				case 1 :
					if(empty($aid_Img))
					{
						$message .= '[attachimg]' . $aid . '[/attachimg]';
					}
					else
					{
						$message .= '[attachimg]' . $aid_Img[$i] . '[/attachimg]';
						$i=$i+1;
					}
					$attachment = 2;
					break;
			}
		}
		
		DB::query("INSERT INTO ".DB::table('forum_thread')." (fid, posttableid, readperm, price, typeid, sortid, author, authorid, subject, dateline, lastpost, lastposter, displayorder, digest, special, attachment, moderated, status, isgroup, replycredit, closed)
		VALUES ('$_G[fid]', '0', '$readperm', '$price', '$typeid', '$sortid', '$author', '$_G[uid]', '$subject', '$publishdate', '$publishdate', '$author', '$displayorder', '$digest', '$special', '$attachment', '$moderated', '$thread[status]', '$isgroup', '$replycredit', '$closed')");
		$tid = DB::insert_id();
		if($modnewposts['modnewposts'] > 0)
		{
			DB::query("INSERT INTO ".DB::table('common_moderate')." (id, idtype, status, dateline)VALUES ($tid,'tid',0,'".time ()."')");
		}
		
		
		if ($tid == 0 || empty($tid)) {
			echo '{"rs":0}';
			exit ();
		}
		useractionlog ( $_G ['uid'], 'tid' );
		DB::update('common_member_field_home', array('recentnote'=>$subject), array('uid'=>$_G['uid']));
		$pinvisible = $modnewposts['modnewposts'] > 0?-2:0;
		$isanonymous = 0;
		$usesig = 1;
		$htmlon = 0;
		$bbcodeoff = - 1;
		$smileyoff = - 1;
		$parseurloff = false;
		$tagstr = null;
		
		
		/*renxing vote insert*/
		foreach($pollarray['options'] as $polloptvalue) {
			$polloptvalue = dhtmlspecialchars(trim($polloptvalue));
			DB::query("insert into ".DB::table('forum_polloption')." (tid,polloption) values ('".$tid."','".$polloptvalue."')");
		}  
		$polloptionpreview = '';
		$query_list = DB::query("SELECT * FROM ".DB::table('forum_polloption')." WHERE tid=".$tid." ORDER BY displayorder LIMIT 2");
		while ($query_rst = DB::fetch($query_list)) {
			$query[] = $query_rst;
		}
		 
		foreach($query as $option) {
			$polloptvalue = preg_replace("/\[url=(https?){1}:\/\/([^\[\"']+?)\](.+?)\[\/url\]/i", "<a href=\"\\1://\\2\" target=\"_blank\">\\3</a>", $option['polloption']);
			$polloptionpreview .= $polloptvalue."\t";
		}
		
		$polloptionpreview = daddslashes($polloptionpreview);
		
		DB::query("insert into ".DB::table('forum_poll')." (tid,multiple,visible,maxchoices,expiration,overt,pollpreview) values ('".$tid."','".$pollarray['multiple']."','".$pollarray['visible']."','".$pollarray['maxchoices']."','".$pollarray['expiration']."','".$pollarray['overt']."','".$polloptionpreview."')");
		/*end  renxing vote insert*/
		
		
		$message = htmlspecialchars_decode ( $message );
		$tagstr = addthreadtag($_GET ['tags'], $tid);
		$message = preg_replace ( '/\[attachimg\](\d+)\[\/attachimg\]/is', '[attach]\1[/attach]', $message );
		$message = str_replace( "&dquot;", '\\"', $message);
		$message = str_replace( "&squot;", "\\'", $message);
		$message = $_GET['platType'] ==1 ? $message."\r\n[url=/mobcent/download/down.php]".Common::get_unicode_charset('\u6765\u81ea\u5b89\u5353\u5ba2\u6237\u7aef').'[/url]':$message."\r\n[url=/mobcent/download/down.php]".Common::get_unicode_charset('\u6765\u81ea\u0069\u0070\u0068\u006f\u006e\u0065\u5ba2\u6237\u7aef')."[/url]";
		$pid = insertpost ( array (
				'fid' => $_G ['fid'],
				'tid' => $tid,
				'first' => '1',
				'author' => $_G ['username'],
				'authorid' => $_G ['uid'],
				'subject' => $subject,
				'dateline' => time (),
				'message' => $message,
				'useip' => get_client_ip (),
				'invisible' => $pinvisible,
				'anonymous' => $isanonymous,
				'usesig' => $usesig,
				'htmlon' => $htmlon,
				'bbcodeoff' => 0,  
				'smileyoff' => $smileyoff,
				'parseurloff' => $parseurloff,
				'attachment' => $attachment,
				'tags' => $tagstr,
				'replycredit' => 0,
				'status' => (defined ( 'IN_MOBILE' ) ? 8 : 0)
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
 		
		if(empty($aid_Img) )
		{
			$threadimageaid = $aid;
			if ($aid) {
				$tableid = getattachtableid ( $tid );
				$attach = DB::fetch_first("SELECT * FROM ".DB::table('forum_attachment_unused')." WHERE aid='$aid' AND uid='".$_G ['uid']."'");
				$aids = $attach ['aid'];
				$data = $attach;
		
				$data ['uid'] = 1;
				$data ['tid'] = $tid;
				$data ['pid'] = $pid;
				DB::insert(getattachtablebytid($tid), $data, false, true);
				DB::update('forum_attachment', array('tid' => $tid, 'pid' => $pid, 'tableid' => getattachtableid($tid)), "aid='$aids'");
				DB::delete('forum_attachment_unused', "aid='$aids'");
		
			}
		
			$values = array (
					'fid' => $_G ['fid'],
					'tid' => $tid,
					'pid' => $pid,
					'coverimg' => ''
			);
			$param = array ();
			if ($_G ['forum'] ['picstyle']) {
				if (! setthreadcover ( $pid, 0, $threadimageaid )) {
					preg_match_all ( "/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER );
					$values ['coverimg'] = "<p id=\"showsetcover\">" . lang ( 'message', 'post_newthread_set_cover' ) . "<span id=\"setcoverwait\"></span></p><script>if($('forward_a')){\$('forward_a').style.display='none';setTimeout(\"$('forward_a').style.display=''\", 5000);};ajaxget('forum.php?mod=ajax&action=setthreadcover&tid=$tid&pid=$pid&fid=$_G[fid]&imgurl={$imglist[0][2]}&newthread=1', 'showsetcover', 'setcoverwait')</script>";
					$param ['clean_msgforward'] = 1;
					$param ['timeout'] = $param ['refreshtime'] = 15;
				}
			}
			
			if(!empty($imagearr)){
				
			}
			if ($threadimageaid && empty($imagearr)) {
				if (! $threadimage) {
					$threadimage = C::t ( 'forum_attachment_n' )->fetch ( 'tid:' . $tid, $threadimageaid );
				}
				$threadimage = daddslashes ( $threadimage );
				C::t ( 'forum_threadimage' )->insert ( array (
				'tid' => $tid,
				'attachment' => $threadimage ['attachment'],
				'remote' => $threadimage ['remote']
				) );
			}
		}
		else
		{
			$isInsertForumImage = false;
			foreach($aid_Img as $key=>$val)
			{
				$threadimageaid = $val;
				if ($val) {
					$tableid = getattachtableid ( $tid );
					$attach = DB::fetch_first("SELECT * FROM ".DB::table('forum_attachment_unused')." WHERE aid='$val' AND uid='".$_G ['uid']."'");
					$aids = $attach ['aid'];
					$data = $attach;
						
					$data ['uid'] = 1;
					$data ['tid'] = $tid;
					$data ['pid'] = $pid;
					DB::insert(getattachtablebytid($tid), $data, false, true);
					DB::update('forum_attachment', array('tid' => $tid, 'pid' => $pid, 'tableid' => getattachtableid($tid)), "aid='$aids'");
					DB::delete('forum_attachment_unused', "aid='$aids'");
		
				}
		
				$values = array (
						'fid' => $_G ['fid'],
						'tid' => $tid,
						'pid' => $pid,
						'coverimg' => ''
				);
				$param = array ();
				if ($_G ['forum'] ['picstyle']) {
					if (! setthreadcover ( $pid, 0, $threadimageaid )) {
						preg_match_all ( "/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER );
						$values ['coverimg'] = "<p id=\"showsetcover\">" . lang ( 'message', 'post_newthread_set_cover' ) . "<span id=\"setcoverwait\"></span></p><script>if($('forward_a')){\$('forward_a').style.display='none';setTimeout(\"$('forward_a').style.display=''\", 5000);};ajaxget('forum.php?mod=ajax&action=setthreadcover&tid=$tid&pid=$pid&fid=$_G[fid]&imgurl={$imglist[0][2]}&newthread=1', 'showsetcover', 'setcoverwait')</script>";
						$param ['clean_msgforward'] = 1;
						$param ['timeout'] = $param ['refreshtime'] = 15;
					}
				}
		
				if (!$isInsertForumImage && $threadimageaid) {
					if (! $threadimage) {
						$threadimage = DB::fetch_first("SELECT attachment, remote FROM ".DB::table(getattachtablebytid($tid))." WHERE aid='$threadimageaid'");
					}
					$threadimage = daddslashes ( $threadimage );
					DB::insert('forum_threadimage', array(
					'tid' => $tid,
					'attachment' => $threadimage['attachment'],
					'remote' => $threadimage['remote'],
					));
		
					$isInsertForumImage = true;
				}
			}
		
		
		}
		
		
		
		$feed = array(
				'icon' => '',
				'title_template' => '',
				'title_data' => array(),
				'body_template' => '',
				'body_data' => array(),
				'title_data'=>array(),
				'images'=>array()
		);
		if(1){
			$message = !$price && !$readperm ? $message : '';
			if($special == 0) {
				$feed['icon'] = 'thread';
				$feed['title_template'] = 'feed_thread_title';
				$feed['body_template'] = 'feed_thread_message';
				$feed['body_data'] = array(
						'subject' => "<a href=\"forum.php?mod=viewthread&tid=$tid\">$subject</a>",
						'message' => messagecutstr($message, 150)
				);
				if(!empty($_G['forum_attachexist'])) {
					$imgattach = C::t('forum_attachment_n')->fetch_max_image('tid:'.$tid, 'pid', $pid);
					$firstaid = $imgattach['aid'];
					unset($imgattach);
					if($firstaid) {
						$feed['images'] = array(getforumimg($firstaid));
						$feed['image_links'] = array("forum.php?mod=viewthread&do=tradeinfo&tid=$tid&pid=$pid");
					}
				}
			} elseif($special > 0) {
				if($special == 1) {
					$pvs = explode("\t", messagecutstr($polloptionpreview, 150));
					$s = '';
					$i = 1;
					foreach($pvs as $pv) {
						$s .= $i.'. '.$pv.'<br />';
					}
					$s .= '&nbsp;&nbsp;&nbsp;...';
					$feed['icon'] = 'poll';
					$feed['title_template'] = 'feed_thread_poll_title';
					$feed['body_template'] = 'feed_thread_poll_message';
					$feed['body_data'] = array(
							'subject' => "<a href=\"forum.php?mod=viewthread&tid=$tid\">$subject</a>",
							'message' => $s
					);
				} elseif($special == 3) {
					$feed['icon'] = 'reward';
					$feed['title_template'] = 'feed_thread_reward_title';
					$feed['body_template'] = 'feed_thread_reward_message';
					$feed['body_data'] = array(
							'subject'=> "<a href=\"forum.php?mod=viewthread&tid=$tid\">$subject</a>",
							'rewardprice'=> $rewardprice,
							'extcredits' => $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][2]]['title'],
					);
				} elseif($special == 4) {
					$feed['icon'] = 'activity';
					$feed['title_template'] = 'feed_thread_activity_title';
					$feed['body_template'] = 'feed_thread_activity_message';
					$feed['body_data'] = array(
							'subject' => "<a href=\"forum.php?mod=viewthread&tid=$tid\">$subject</a>",
							'starttimefrom' => $_GET['starttimefrom'][$activitytime],
							'activityplace'=> $activity['place'],
							'message' => messagecutstr($message, 150),
					);
					if($_GET['activityaid']) {
						$feed['images'] = array(getforumimg($_GET['activityaid']));
						$feed['image_links'] = array("forum.php?mod=viewthread&do=tradeinfo&tid=$tid&pid=$pid");
					}
				} elseif($special == 5) {
					$feed['icon'] = 'debate';
					$feed['title_template'] = 'feed_thread_debate_title';
					$feed['body_template'] = 'feed_thread_debate_message';
					$feed['body_data'] = array(
							'subject' => "<a href=\"forum.php?mod=viewthread&tid=$tid\">$subject</a>",
							'message' => messagecutstr($message, 150),
							'affirmpoint'=> messagecutstr($affirmpoint, 150),
							'negapoint'=> messagecutstr($negapoint, 150)
					);
				}
			}
		
			$feed['title_data']['hash_data'] = "tid{$tid}";
			$feed['id'] = $tid;
			$feed['idtype'] = 'tid';
			if($feed['icon']) {
				postfeed($feed);
			}
		}
		
		if($digest) {
			updatepostcredits('+',  $_G['uid'], 'digest', $_G['fid']);
		}
		updatepostcredits('+',  $_G['uid'], 'post', $_G['fid']);
		if($isgroup) {
			C::t('forum_groupuser')->update_counter_for_user($_G['uid'], $_G['fid'], 1);
		}
		if (! $pid) {
			$obj -> rs = SUCCESS;
			echo echo_json($obj);
			exit ();
		}
		$subject = str_replace ( "\t", ' ', $subject );
		$lastpost = "$tid\t" . $subject . "\t$publishdate\t$author";
		DB::query("UPDATE ".DB::table('forum_forum')." SET lastpost='$lastpost' ,threads=threads+1, posts=posts+1, todayposts=todayposts+1 WHERE fid='".$fid."'", 'UNBUFFERED');
		DB::query("UPDATE ".DB::table('forum_thread')." SET lastposter='$author', lastpost=".time().", replies=replies+1 WHERE tid='".$tid."'", 'UNBUFFERED');
		
		if(isset($rPostion) && !empty($rPostion))
		{
			C::t('home_surrounding_user')->insert_all_thread_location($longitude,$latitude,$location,$pid);
		}
		$data_post ["rs"] = 1;
		$data_post ["content"] = $modnewposts['modnewposts'] > 0?Common::get_unicode_charset('\u65b0\u4e3b\u9898\u9700\u8981\u5ba1\u6838\uff0c\u60a8\u7684\u5e16\u5b50\u901a\u8fc7\u5ba1\u6838\u540e\u624d\u80fd\u663e\u793a'):'';
		return $data_post;
		}

}

?>