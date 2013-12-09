<?php
define('IN_MOBCENT',1);
require_once './abstractPostPerm.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
C::app ()->init();

require_once '../public/mobcentDatabase.php';
require_once libfile('class/credit');
require_once libfile('function/post');
class PostPermImpl_x25 extends abstractPostPerm {
	public function getPostPermObj() {
		loadforum();
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
		}else {
			$group = $info-> sel_group_by_uid($uid);
			
		}
		//print_r($group[groupid]);exit;
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
		
		/*rx new added 20130827 yan zheng email cai ke yi fa tie*/
		$setting_query = DB::query("SELECT * FROM ".DB::table('common_setting'));
		while($setting_list = DB::fetch($setting_query)) {
			$setting_arr[] = $setting_list;
		}
		foreach($setting_arr as $sa){
			if($sa[skey]=='need_email'){
				$need_email=$sa[svalue];
			}
		}
		$user_query = DB::fetch(DB::query("SELECT * FROM ".DB::table('common_member').' where uid='.$uid));
		$user_email=$user_query[emailstatus];
		if($need_email==1 && $user_email==0){
			$data_post['rs'] = 0;
			$data_post['errcode'] = '06000001';
			return $data_post;
			exit();
		}
		/*---ban kuai quan xian---*/
		$board_query = DB::fetch(DB::query("SELECT * FROM ".DB::table('forum_forumfield')." where fid=".$_G['fid']));
		$board_arr=explode("	", $board_query[postperm]);
		unset($board_arr[0]);
		if(!empty($board_arr)){
			if(!in_array($group[groupid], $board_arr)){
				$data_post['rs'] = 0;
				$data_post['errcode'] = '01110001';
				return $data_post;
				exit();
			}
		}
		/*end rx 20130827*/
		
		
		require_once '../public/yz.php';
		$checkObj = new check();
		$resulst = $checkObj->postperm($_G['fid'],$forum['postperm']);
		if($resulst['error']){
			$data_post ["rs"] = 0;
			$data_post ["list"] =array();
		}else{
			$data_post ["rs"] = 1;
			$forum = C::t('forum_forum')->fetch_all_info_by_fids($_G['fid']);
			$bb =decbin($forum[$_G['fid']]['allowpostspecial']);
			
			$bb =substr($bb, -1,1);
			$isyes = $bb==1 && $group['allowpostpoll']==1?1:0;
			$forum_arr = array( 'specil' =>$isyes,'topic' =>1,'classify' =>1);
			$data_post ["list"] =$forum_arr;
		}
		
		return $data_post;
	}
}

?>