<?php
require_once './abstractCommonPictureSet.php';
define ( 'IN_MOBCENT', 1 );
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
include_once '../Config/public.php';
require_once '../public/yz.php';
require_once '../tool/Thumbnail.php';
require_once '../public/common_json.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once libfile ( 'function/forumlist' );
require_once '../model/table/x20/mobcentDatabase.php';
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/table_common_member.php';

class CommonPictureSetImpl_x20 extends abstractCommonPictureSet {
	public function getCommonPictureSetObj() {
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
		setglobal('groupid', $group['groupid']);
		global $_G;
	$page = $_GET ['page'] ? $_GET ['page'] : 1;
	$forumId = $_GET ['forumId'];
	$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20; 
	if($page == 1)
	{
		$start_limit = 0;
	}
	else {
		
	$start_limit = ($page - 1) * $limit - 1; 
	}
	$order = ($_GET ['sortby'] == 'publish' ? 'tid' : 'displayorder,lastpost'); 
	$parameter = array (
			'forum_thread',
			'',
	);
	$picNew = new topic();
	$tids =$info->forum_check_content($uid,$picNew);
	$threadlist =C::t('commonpictureset') -> fetch_all_thread_img($start_limit, $limit,$tids);
	foreach ( $threadlist as $k => $group ) {
		$data ['forumId'] = $forumId;
		$data ['board_id'] = ( int ) $group ['fid'];  
		$borderName=C::t('forum_forum')->fetch_all_name_by_fid($group ['fid']);
		$data ['board_name'] =$borderName[$group ['fid']]['name'];
		$data ['topic_id'] = ( int ) $group ['tid'];  
		$data ['title'] = $group['subject'];
		$data ['user_id'] = ( int ) $group ['authorid'];  
		$data ['last_reply_date'] = ($group ['lastpost']) . "000";  
		$data ['user_nick_name'] = $group ['author'];  
		$data ['hits'] = ( int ) $group ['views'];  
		$data ['replies'] = ( int ) $group ['replies'];  
		$data ['top'] = ( int ) $group ['first'];  
		$data ['status'] = ( int ) $group ['status'];  
		$data ['essence'] = ( int ) $group ['digest'] ? 1 : 0; 
		$data ['hot'] = ( int ) $group ['stamp'] > 0 ? 1 : 0;  
		
		$pic_path = $picNew->parseTargeThumbImage($group);
		$path = dirname (__FILE__)  .'/../../'.$pic_path;
		list($width,$height)=getimagesize($path);
		$data ['pic_path'] = $pic_path;
		$data ['ratio']=$height/$width;
	
		unset ( $pic_path );
		$data_thread [] = $data;
	}
	
	
	$count = DB::fetch_first ( "SELECT count(*) as num  FROM ".DB::table('forum_thread')." as t right join ".DB::table('forum_threadimage')." as img  on t.tid=img.tid WHERE img.attachment !='' AND t.displayorder != -2 and t.fid in(".$tids.")");
	
	if($count['num']-($page * $limit) > 0)
	{
		$has_next =(int)1;
	}
	else
	{
		$has_next = (int)0;
	}
	$data_post = array (
				"img_url" => DISCUZSERVERURL,
				"total_num" => (Int)$count['num'],
				"page" => (Int)$page,
				"has_next" => $has_next,
				'list' => $data_thread,
				'rs' => 1
		);
		return $data_post;
		}

}

?>