<?php
require_once './abstractFavoriteInfo.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../Config/public.php';
require_once '../tool/tool.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();

class FavoriteInfoImpl_x25 extends abstractFavoriteInfo {
	public function getAddFavoriteInfoObj() {
		$rs=0;
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
		$_G['uid'] = $uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = 500000;
			return $data_post;
			exit();
		}
		$_GET['type']	=$_GET['type'] ?$_GET['type']:'thread';
		$GETid  		=$_GET['topicId']?$_GET['topicId']:'';
		$_GET['spaceuid'] 	= $_GET['spaceuid']?$_GET['spaceuid']:0;
		$_POST['favoritesubmit'] = $_POST['favoritesubmit']?$_POST['favoritesubmit']:true;
		$_POST['description'] = $_POST['description']?$_POST['description']:Common::get_unicode_charset('\u7528\u79fb\u52a8\u5ba2\u6237\u7aef\u6536\u85cf');
		$_GET['type'] = in_array($_GET['type'], array("thread", "forum", "group", "blog", "album", "article", "all")) ? $_GET['type'] : 'all';
		$type = empty($_GET['type']) ? 'thread' : $_GET['type'];
		$id = empty($GETid) ? 0 : intval($GETid);
		$spaceuid = empty($_GET['spaceuid']) ? 0 : intval($_GET['spaceuid']);
		$idtype = $title = $icon = '';
		switch($type) {
			case 'thread':
				$idtype = 'tid';
				$thread = C::t('forum_thread')->fetch($id);
				$title = $thread['subject'];
				$icon = '<img src="static/image/feed/thread.gif" alt="thread" class="vm" /> ';
				break;
			case 'forum':
				$idtype = 'fid';
				$foruminfo = C::t('forum_forum')->fetch($id);
				$title = $foruminfo['status'] != 3 ? $foruminfo['name'] : '';
				$icon = '<img src="static/image/feed/discuz.gif" alt="forum" class="vm" /> ';
				break;
			case 'blog';
			$idtype = 'blogid';
			$bloginfo = C::t('home_blog')->fetch($id);
			$title = ($bloginfo['uid'] == $spaceuid) ? $bloginfo['subject'] : '';
			$icon = '<img src="static/image/feed/blog.gif" alt="blog" class="vm" /> ';
			break;
			case 'group';
			$idtype = 'gid';
			$foruminfo = C::t('forum_forum')->fetch($id);
			$title = $foruminfo['status'] == 3 ? $foruminfo['name'] : '';
			$icon = '<img src="static/image/feed/group.gif" alt="group" class="vm" /> ';
			break;
			case 'album';
			$idtype = 'albumid';
			$result = C::t('home_album')->fetch($id, $spaceuid);
			$title = $result['albumname'];
			$icon = '<img src="static/image/feed/album.gif" alt="album" class="vm" /> ';
			break;
			case 'space';
			$idtype = 'uid';
			$_member = getuserbyuid($id);
			$title = $_member['username'];
			$unset($_member);
			$icon = '<img src="static/image/feed/profile.gif" alt="space" class="vm" /> ';
			break;
			case 'article';
			$idtype = 'aid';
			$article = C::t('portal_article_title')->fetch($id);
			$title = $article['title'];
			$icon = '<img src="static/image/feed/article.gif" alt="article" class="vm" /> ';
			break;
		}
		if(empty($idtype) || empty($title)) {
			$error=Common::get_unicode_charset('\u62b1\u6b49\uff0c\u60a8\u6307\u5b9a\u7684\u4fe1\u606f\u65e0\u6cd5\u6536\u85cf');
		}
		
		$fav = C::t('home_favorite')->fetch_by_id_idtype($id, $idtype, $_G['uid']);
		if($fav) {
			$error=Common::get_unicode_charset('\u62b1\u6b49\uff0c\u60a8\u5df2\u6536\u85cf\uff0c\u8bf7\u52ff\u91cd\u590d\u6536\u85cf');
		}
		$description = '';
		$description_show = nl2br($description);
		
		$fav_count = C::t('home_favorite')->count_by_id_idtype($id, $idtype);
		if($_POST['favoritesubmit'] || $type == 'forum' || $type == 'group') {
			$arr = array(
					'uid' => intval($_G['uid']),
					'idtype' => $idtype,
					'id' => $id,
					'spaceuid' => $thread['authorid'],
					'title' => getstr($title, 255),
					'description' => getstr($_POST['description'], '', 0, 0, 1),
					'dateline' => TIMESTAMP
			);
			$favid = C::t('home_favorite')->insert($arr, true);
			if($_G['setting']['cloud_status']) {
				$favoriteService = Cloud::loadClass('Service_Client_Favorite');
				$favoriteService->add($arr['uid'], $favid, $arr['id'], $arr['idtype'], $arr['title'], $arr['description'], TIMESTAMP);
			}
			switch($type) {
				case 'thread':
					C::t('forum_thread')->increase($id, array('favtimes'=>1));
					require_once libfile('function/forum');
					update_threadpartake($id);
					break;
				case 'forum':
					C::t('forum_forum')->update_forum_counter($id, 0, 0, 0, 0, 1);
					dsetcookie('nofavfid', '', -1);
					break;
				case 'blog':
					C::t('home_blog')->increase($id, $spaceuid, array('favtimes' => 1));
					break;
				case 'group':
					C::t('forum_forum')->update_forum_counter($id, 0, 0, 0, 0, 1);
					break;
				case 'album':
					C::t('home_album')->update_num_by_albumid($id, 1, 'favtimes', $spaceuid);
					break;
				case 'space':
					C::t('common_member_status')->increase($id, array('favtimes' => 1));
					break;
				case 'article':
					C::t('portal_article_count')->increase($id, array('favtimes' => 1));
					break;
			}
			$success=Common::get_unicode_charset('\u4fe1\u606f\u6536\u85cf\u6210\u529f');
			$rs=1;
		}
		
		if($rs){
			$data_post ["rs"] = 1;
		}else{
			$data_post ["rs"] = 0;
		}
			
		
		return $data_post;
			}
	public function getDelFavoriteInfoObj()
	{
		$rs=0;
		$info = new mobcentGetInfo();
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$_G['uid'] = $uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		if(!$_G['uid']){
			echo echo_json(array("rs"=>0,"error"=>'error'));exit();
		}
		$_GET['type']		=$_GET['type'] ?$_GET['type']:'thread';
		$GETid  		=$_GET['topicId']?$_GET['topicId']:4;
		
		$_GET['spaceuid'] 	= $_GET['spaceuid']?$_GET['spaceuid']:0;
		$_POST['favoritesubmit'] = $_POST['favoritesubmit']?$_POST['favoritesubmit']:true;
		$_POST['description'] = $_POST['description']?$_POST['description']:Common::get_unicode_charset('\u7528\u79fb\u52a8\u5ba2\u6237\u7aef\u6536\u85cf');
		$_GET['type'] = in_array($_GET['type'], array("thread", "forum", "group", "blog", "album", "article", "all")) ? $_GET['type'] : 'all';
		
		
		if($_GET['checkall']) {
			if($_GET['favorite']) {
				C::t('home_favorite')->delete($_GET['favorite'], false, $_G['uid']);
				if($_G['setting']['cloud_status']) {
					$favoriteService = Cloud::loadClass('Service_Client_Favorite');
					$favoriteService->remove($_G['uid'], $_GET['favorite'], TIMESTAMP);
				}
			}
			$data_post ["rs"] = 1;
			return $data_post;
			exit();
		} else {
		
		
			$favid = intval($_GET['favid']);
			$type = empty($_GET['type']) ? 'thread' : $_GET['type'];
			switch($type) {
				case 'thread':
					$idtype = 'tid';
					break;
				case 'forum':
					$idtype = 'fid';
					break;
				case 'blog';
				$idtype = 'blogid';
				break;
				case 'group';
				$idtype = 'gid';
				break;
				case 'album';
				$idtype = 'albumid';
				break;
				case 'space';
				$idtype = 'uid';
				break;
				case 'article';
				$idtype = 'aid';
				break;
			}
			$thevalue = C::t('home_favorite')->fetch_by_id_idtype($GETid, $idtype, $_G['uid']);
			$favid = $thevalue['favid'];
			if(empty($thevalue) || $thevalue['uid'] != $_G['uid']) {
				$error='error';
				echo echo_json(array("rs"=>0,"error"=>$error));exit();
			}else {
				C::t('home_favorite')->delete($favid);
				if($_G['setting']['cloud_status']) {
					$favoriteService = Cloud::loadClass('Service_Client_Favorite');
					$favoriteService->remove($_G['uid'], $favid);
				}
				$data_post ["rs"] = (Int)1;
			}
		}
		return $data_post;
	}
}

?>