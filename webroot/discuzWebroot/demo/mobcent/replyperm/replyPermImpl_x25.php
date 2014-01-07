<?php
require_once './abstractReplyPerm.php';
define('IN_MOBCENT',1);
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
C::app ()->init();
require_once '../public/mobcentDatabase.php';

class replyPermImpl_x25 extends abstractReplyPerm {
	public function getReplyPermObj() {
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
		$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
		if(empty($accessSecret) || empty($accessToken))
		{
			$group = $info-> sel_QQuser($qquser);
		
		}else if(empty($_G ['uid']))
		{
			return $info -> userAccessError();
			exit();
		}
		else {
			$group = $info-> sel_group_by_uid($uid);
			
		}
		if(!$_G['forum']['viewperm'] && !$group['readaccess'])
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '01110001';
			return $data_post;
			exit();
		}
		$space = $info->getUserInfo(intval($uid));
		$_G=array_merge($_G,$space);
		$_G['fid'] = intval($_GET['boardId']);
		$forum = $info->getForumSub($_G['fid']);
		$_G['forum']=array_merge($_G['forum'],$forum);
		
		require_once '../public/yz.php';
		$checkObj = new check();
		$resulst = $checkObj->replyperm($_G['fid'],$forum['postperm']);
		if($resulst['error']){
			echo $resulst['message'];exit();
		}else{
			$data_post ["rs"] = 1;
		}
		return $data_post;
			}
		}

?>