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
require_once ('./abstractBoardChild.php');
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_common_member.php';
require_once '../model/table/x25/table_forum_typeoptionvar.php';
require_once '../model/table/x25/table_forum_threadtype.php';
require_once '../model/table/x25/table_forum_threadclass.php';
require_once '../../source/class/table/table_forum_forum.php';
define('ALLOWGUEST', 1);
C::app ()->init();
require_once '../public/mobcentDatabase.php';

class boardChildImpl_x25 extends abstractBoardChild {
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
		$boardLineNum= empty($_GET['boardLineNum']) ? 2 : 1;
		$gid = $_GET['boardId']?intval($_GET['boardId']):-1;
		$topicInstance = new topic();
		
		$child_query=DB::query("select a.*,b.icon from ".DB::table("forum_forum")." a, ".DB::table("forum_forumfield")." b where a.fid=b.fid AND a.fup=".$gid);
		while($child_list=DB::fetch($child_query)){
			$query[]=$child_list;
		} 
		if(is_array($query)){
			foreach ( $query as $k => $forum ) {
				$lasts = DB::fetch(DB::query("SELECT fid,lastpost FROM ".DB::table('forum_thread').' where fid='.$forum["fid"].' order by lastpost desc limit 1'));
				$data_forum [] = array (
						"board_id"			=>(int)$forum["fid"],
						"board_name"		=>$forum["name"],
						"board_child"		=>0,
						"board_img"			=>empty($forum["icon"])?"":"/data/attachment/common/".$forum["icon"],
						"board_content"		=>1,
						"td_posts_num"		=>(int)$forum["todayposts"],
						"topic_total_num"	=>(int)$forum["threads"],
						"posts_total_num"	=>(int)$forum["posts"],
						"last_posts_date"	=>$lasts['lastpost'].'000',
				);
			}
		}else{
			$data_forum[]= array();
		}
		
		if(count($query)==0){
			$data_cat['boardId'] = (int)$gid;
			//$data_cat['board_category_type'] = $boardLineNum;
			$data_cat['board_category_type'] = 1;//qiang zhi dan lie 20130924
			$data_cat['childList'] = array();
			$data_cat['rs'] = (int)1;
		}else{
			$data_cat['boardId'] = (int)$gid;
			//$data_cat['board_category_type'] = $boardLineNum;
			$data_cat['board_category_type'] = 1;//qiang zhi dan lie 20130924
			$data_cat['childList'] = empty($data_forum)?array():$data_forum;
			$data_cat['rs'] = (int)1;
		}
		return $data_cat;
	}


}

?>