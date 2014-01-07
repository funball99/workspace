<?php
require_once './abstarctAnnouncement.php';
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../tool/Thumbnail.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/img_do.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once libfile('function/forumlist');
require_once libfile('function/discuzcode');
require_once libfile('function/post');
require_once '../model/table/x20/mobcentDatabase.php';

class AnnouncementListImpl_x20 extends abstarctAnnouncement {
	public function getAnnouncementListObj() {
		$announceId = $_GET['announceId'];
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
		$timestamp = time();
		$query = DB::query("SELECT id, subject, groups, author, starttime, endtime, message, type FROM ".DB::table('forum_announcement')." WHERE type!=2 AND starttime<='$timestamp' AND (endtime='0' OR endtime>'$timestamp') AND id = '$announceId' ORDER BY displayorder, starttime DESC, id DESC");
		
		$announcelist = array();
		while($announce = DB::fetch($query)) {
			$announce['announce_id'] = $announce['id'];
			$announce['author'] = $announce['author'];
			$tmp = explode('.', dgmdate($announce['starttime'], 'Y.m'));
			$months[$tmp[0].$tmp[1]] = $tmp;
			if(!empty($_GET['m']) && $_GET['m'] != dgmdate($announce['starttime'], 'Ym')) {
				continue;
			}
			$announce['start_date'] = dgmdate($announce['starttime'], 'd');
			$announce['title'] = preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$announce['subject']);
			$announce['board_id'] = 0;
			$announce['forum_id'] = 0;
			$uid = DB::query("SELECT uid FROM ".DB::table('common_member')." WHERE username = '".$announce['author']."'");
			$uids = DB::fetch($uid);
			$announce ['icon']		=userIconImg($uids['uid']);
			$announce['message'] = $announce['type'] == 1 ? "[url]{$announce[message]}[/url]" : $announce['message'];
			$announce['message'] = nl2br(discuzcode($announce['message'], 0, 0, 1, 1, 1, 1, 1));
			$message1 = doContent ( $announce ['message'] );
				
			$message2 = getContentFont ( $announce ['message'] );
				
			foreach($message1 as $k=>$v){
				if($v['type']==0){
					unset($message1[$k]);
				}else{
						
				}
			}
				
			$message_array2 = explode('|~|', $message2);
			$message2 = str_replace('[', '1', $message2);
			$message2 = str_replace(']', '1', $message2);
			if(is_array($message_array2) && count($message_array2)>0){
					
				foreach($message_array2 as $k=>$v){
					$message[]=array("infor" =>$v,"type"=>0);
					if($message1[$k]["infor"] && !empty($message1)){
						$message[]=$message1[$k];
					}
				}
			}else{
				$message =getContentFont($announce ['message']);
			}
			$announce ['announce_content']= $message;
			$announcelist = $announce;
		}
		$data['img_url'] = '';
		$data['icon'] = '';
		$data['announce_detail'] = $announcelist;
		$data['rs'] = (Int)1;
		return $data;
		}

}

?>