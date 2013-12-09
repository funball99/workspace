<?php
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../source/class/table/table_forum_forumfield.php';
require_once '../../config/config_ucenter.php';
require_once libfile('function/forumlist');
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/img_do.php';
require_once '../public/yz.php';
require_once '../Config/public.php';
require_once '../tool/Thumbnail.php';
require_once '../tool/constants.php';
require_once ('./abstractTypeList.php');
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_common_member.php';
require_once '../model/table/x25/table_forum_typeoptionvar.php';
require_once '../model/table/x25/table_forum_threadtype.php';
require_once '../model/table/x25/table_forum_threadclass.php';
require_once '../../source/class/table/table_forum_forum.php';
define('ALLOWGUEST', 1);
C::app ()->init();
require_once '../public/mobcentDatabase.php';

class typeListImpl_x25 extends abstractTypeList {
	function getSubBoardList() {
		$info = new mobcentGetInfo();
		$accessSecret = $_GET['accessSecret']?$_GET['accessSecret']:'';
		$accessToken = $_GET['accessToken']?$_GET['accessToken']:'';
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
		$gid = $_GET['boardId']?intval($_GET['boardId']):0;
		$topicInstance = new topic();
		
		/*email yan zheng*/
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
		/*end email yan zheng*/
		/*---ban kuai quan xian---*/
		$board_query = DB::fetch(DB::query("SELECT * FROM ".DB::table('forum_forumfield')." where fid=".$gid));
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
		/*---end ban kuai quan xian---*/
		
		/*fenlei*/
		$forumfield= C::t ( 'forum_forumfield' )->fetch_all_by_fid ($gid);
		$fenlei=unserialize($forumfield[$gid][threadsorts]);
		foreach($fenlei['types'] as $fl_key=>$fl_val){
			$key_arr[]=$fl_key;
			$val_arr[]=$fl_val;
		}
		for($i=0;$i<count($key_arr);$i++){
			$fenlei_arr[$i]['classificationTop_id']=$key_arr[$i];
			$fenlei_arr[$i]['classificationTop_name']=$topicInstance->replaceHtmlAndJs($val_arr[$i]);
		}
		/* end fenlei */

		/*lei bie*/
		$forum = C::t('forum_forum')->fetch_info_by_fid($gid);
		$leibie_query=unserialize($forum[threadtypes]);
		foreach($leibie_query[types] as $leibiekey=>$leibieval){
			$leibie_list['classificationType_id']=$leibiekey;
			$leibie_list['classificationType_name']=$topicInstance->replaceHtmlAndJs($leibieval);
			$leibie_arr[]=$leibie_list;
		}
		/* end lei bie */
		
		if(empty($leibie_arr)){
			$data_cat['boardId'] = (int)$gid;
			$data_cat['typeList'] = array();
			$data_cat['rs'] = (int)1;
		}else{
			$data_cat['boardId'] = (int)$gid;
			$data_cat['typeList'] = empty($leibie_query)?array():$leibie_arr;
			$data_cat['rs'] = (int)1;
		}
		return $data_cat;
	}


}

?>