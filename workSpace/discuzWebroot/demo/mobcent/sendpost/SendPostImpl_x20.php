<?php
require_once './abstractSendPost.php';
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

class SendPostImpl_x20 extends abstractSendPost {
	public function getSendPostObj() {
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
		$special = 0;
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
		
		
		/*renxing x20 fenlei insert begin 2013-6-17*/ 
		$get_classified=echo_array((urldecode($_GET['classified']))); 
 		$array_message=echo_array(urldecode($_GET['content'])); 
		foreach($get_classified as $g_k=>$g_v){			
			$_GET[$g_k]=$g_v;
		} 
		
		/*---begin insert---*/
		if(isset($_GET['classificationTopId']) && $_GET['classificationTopId']!=0){
			$fenlei_id = intval($_GET['classificationTopId']);		

			/*table:forum_typeoption*/
			$classified_query = DB::query("SELECT a.optionid,a.classid as classifiedTopId,a.title as classifiedTitle,a.identifier as classifiedName,a.type as classifiedType,a.rules as classifiedRules,b.* FROM ".DB::table('forum_typeoption')." as a left join ".DB::table('forum_typevar')." as b  on a.optionid=b.optionid WHERE b.sortid=".$fenlei_id." order by b.displayorder asc");
			while ($classified_result = DB::fetch($classified_query)) {
				$classified_arr[] = $classified_result;
			}			
			 
			$optionvar=array();
			$checkvar=array();
			for($i=0;$i<count($classified_arr);$i++){

				/*the para that to check the data*/
				$optionvar['value']=$_GET[$classified_arr[$i][classifiedName]];
				$checkvar['dataTitle']=$classified_arr[$i][classifiedTitle];/* shu ju Title(chinese name)*/
				$checkvar['dataName']=$classified_arr[$i][classifiedName];/* shu ju ming cheng */
				$checkvar['dataType']=$classified_arr[$i][classifiedType];/* shu ju lei xing */
				$checkvar['dataRules']=unserialize($classified_arr[$i][classifiedRules]);/* shu ju gui ze */
				
				$checkvar['available']=$classified_arr[$i][available];/* ke yong?1:0 */
				$checkvar['required']=$classified_arr[$i][required];/* bi tian?1:0 */
				$checkvar['unchangeable']=$classified_arr[$i][unchangeable];/*bu ke xiu gai?1:0 */
				$checkvar['search']=$classified_arr[$i][search];/*wen zi jian suo?2:0 */
				$checkvar['displayorder']=$classified_arr[$i][displayorder];/*xian shi shun xu */
				$checkvar['subjectshow']=$classified_arr[$i][subjectshow];/*zhu ti zhan shi?1:0 */
					
				$flag=false;
				
				/*--if it is ke yong---*/
				if($checkvar['available']==1){
					/*--check bi tian xiang--*/
					if($checkvar['required']==1 && empty($optionvar['value'])){
						$data_post['rs'] = 0;
						$data_post['errorName'] = $checkvar['dataTitle'];
						$data_post['errcode'] = '04000001';
						return $data_post;
						exit();
					}else{
						$flag=true;
					}
				
					/*--check email--*/
					if(($checkvar['required']==1 || !empty($optionvar['value'])) && $checkvar['dataType']=="email"){
						$email=$optionvar['value'];
						$regular="/[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-z]{2,4}/";
						if (preg_match($regular,$email)){
							$flag=true;
						}else{
							$data_post['rs'] = 0;
							$data_post['errorName'] = $checkvar['dataTitle'];
							$data_post['errcode'] = '04000003';
							return $data_post;
							exit();
						}
					}else{
						$flag=true;
					}
						
					 
					/*--check is a number--*/
					if(($checkvar['required']==1 || !empty($optionvar['value'])) && $checkvar['dataType']=="number"){
						if(is_numeric($optionvar['value'])){
							$flag=true;
						}else{
							$data_post['rs'] = 0;
							$data_post['errorName'] = $checkvar['dataTitle'];
							$data_post['errcode'] = '04000005';
							return $data_post;
							exit();
						}
					}else{
						$flag=true;
					}
				
					/*--check the maxnum and minnum for a number--*/
					if($checkvar['required']==1 && $checkvar['dataType']=="number"){
						$maxnum=floatval($checkvar[dataRules][maxnum]);
						$minnum=floatval($checkvar[dataRules][minnum]);
						if(!empty($maxnum) && !empty($minnum) && $maxnum>$minnum){
							if(is_numeric($optionvar['value']) && floatval($optionvar['value'])>=$minnum && floatval($optionvar['value'])<=$maxnum){
								$flag=true;
							}else{
								$data_post['rs'] = 0;
								$data_post['maxnums'] = 0;
								$data_post['errorName'] = $checkvar['dataTitle'];
								$data_post['errcode'] = '04000007';
								return $data_post;
								exit();
							}
						}
					}else{
						$flag=true;
					}
				
					/*--check chang du guo chang--*/
					if($checkvar['required']==1 && $checkvar[dataRules][maxlength]!=""){
						$maxlength=$checkvar[dataRules][maxlength];
						$length=strlen(trim($optionvar['value']));
						if($maxlength>=$length){
							$flag=true;
						}else{
							$data_post['rs'] = 0;
							$data_post['errorName'] = $checkvar['dataTitle'];
							$data_post['errcode'] = '04000006';
							return $data_post;
							exit();
						}
					}else{
						$flag=true;
					}
				}else{
					$flag=true;
				}
					
			}
										
			$max_tid_query = DB::query("SELECT MAX(tid) as maxtid FROM ".DB::table('forum_thread'));
			while ($max_tid_result = DB::fetch($max_tid_query)) {
				$max_tid_arr = $max_tid_result;
			}
			$max_tid = $max_tid_arr[maxtid]; 			
			
			if($flag==true){
				for($i=0;$i<count($classified_arr);$i++){
					/*the para that insert to database */
					$optionvar['sortid']=$sortid=$fenlei_id;
					$optionvar['tid']=($max_tid+1);
					$optionvar['fid']=$_G ['fid'];
					$optionvar['optionid']=$classified_arr[$i][optionid];
					$optionvar['expiration']=0;
					
					if($classified_arr[$i][classifiedType]=="checkbox" && $_GET[$classified_arr[$i][classifiedName]]!=""){
						$optionvar['value']=str_replace(',', '	', $_GET[$classified_arr[$i][classifiedName]]);
					
					}elseif($classified_arr[$i][classifiedType]=="image" && $_GET[$classified_arr[$i][classifiedName]]!=""){
						$imgaid=intval($_GET[$classified_arr[$i][classifiedName]]);
						$pic_list = DB::query("SELECT attachment FROM ".DB::table('forum_attachment_unused')." where aid=".$imgaid);
						while($pic_value = DB::fetch($pic_list)) {
							$pic = $pic_value;
						}
						$pictures_arr=array();
						$pictures_arr['aid']=$imgaid;
						$pictures_arr['url']="data/attachment/forum/".$pic[attachment];
						$optionvar['value']=serialize($pictures_arr);
						$imageaid[]=$imgaid;
						$imagearr[]=$pic[attachment];
						$attachment =2;
					}else{
						$optionvar['value']=$_GET[$classified_arr[$i][classifiedName]];
					}
					DB::query("INSERT INTO ".DB::table('forum_typeoptionvar')." VALUES('".$optionvar['sortid']."','".$optionvar['tid']."',".$optionvar['fid'].",'".$optionvar['optionid']."',".$optionvar['expiration'].",'".$optionvar['value']."')");
					$optionvaluerx[$classified_arr[$i][classifiedName]]=$optionvar['value'];
				}
				$optionvaluerx['fid']=$_G ['fid'];
				$optionvaluerx['tid']=($max_tid+1);
				/*---pin jie sql yu ju---*/
				$rxsql="INSERT INTO ".DB::table('forum_optionvalue'.$fenlei_id)."(";
				foreach($optionvaluerx as $opkey=>$oprx){$rxsql.=$opkey.',';}
				$rxsql=substr($rxsql, 0,-1);
				$rxsql.=") VALUES(";
				foreach($optionvaluerx as $opkey=>$oprx){$rxsql.="'".$oprx."',";}
				$rxsql=substr($rxsql, 0,-1);
				$rxsql.=")";
				/*---end pin jie sql yu ju---*/
				DB::query($rxsql);
			}
		} 
		if(!empty($imagearr)){
			DB::query("INSERT INTO ".DB::table('forum_threadimage')." VALUES('".($max_tid+1)."','".$imagearr[0]."','0')");
		}
		if(isset($_GET['classificationTypeId']) && $_GET['classificationTypeId']!=0){
			$typeid=intval($_GET['classificationTypeId']);
		}
		/*renxing x20 fenlei insert end*/
		 
		
	 
		$subject = echo_urldecode ( $_GET ['title'] ) ;
		 
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
		//DB::query("UPDATE ".DB::table('forum_thread')." SET lastposter='$author', lastpost=".time().", replies=replies+1 WHERE tid='".$tid."'", 'UNBUFFERED');
		
		if(isset($rPostion) && !empty($rPostion))
		{
			C::t('home_surrounding_user')->insert_all_thread_location($longitude,$latitude,$location,$tid);
		}
		$data_post ["rs"] = 1;
		$data_post ["content"] = $modnewposts['modnewposts'] > 0?Common::get_unicode_charset('\u65b0\u4e3b\u9898\u9700\u8981\u5ba1\u6838\uff0c\u60a8\u7684\u5e16\u5b50\u901a\u8fc7\u5ba1\u6838\u540e\u624d\u80fd\u663e\u793a'):'';
		return $data_post;
	}

}

?>