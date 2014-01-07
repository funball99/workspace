<?php
require_once './abstractCommonPictureSet.php';
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
include_once '../Config/public.php';
require_once '../public/yz.php';
require_once '../tool/Thumbnail.php';
require_once '../model/table_commonpictureset.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile ( 'function/forumlist' );
require_once '../public/mobcentDatabase.php';
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_common_member.php';
class CommonPictureSetImpl_x25 extends abstractCommonPictureSet {
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
		global $_G;
		$_G['groupid'] =$group['groupid'];
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$forumId = $_GET ['forumId'];
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20;
		$start_limit = ($page - 1) * $limit;
		$order = ($_GET ['sortby'] == 'publish' ? 'tid' : 'displayorder,lastpost');
		$parameter = array (
				'forum_thread',
				'forum_threadimage',
		);
		$picNew = new topic();
		$tids =$info ->forum_display($fid,$picNew);
		$threadlist =commonpictureset:: fetch_all_thread_img($start_limit, $limit,$tids);
		foreach ( $threadlist as $k => $group ) {
			$data ['forumId'] = $forumId;
			$data ['board_id'] = ( int ) $group ['fid'];
			$borderName=C::t('forum_forum')->fetch_all_name_by_fid($group ['fid']);
			$data ['board_name'] =$borderName[$group ['fid']]['name'];
			$data ['topic_id'] = ( int ) $group ['tid'];
			$data ['title'] = $group ['subject'];
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
			//$pic_path = $picNew->parseTargeImage($group);
			
			$data ['pic_path'] = $pic_path;
			$path = dirname (__FILE__)  .'/../../'.$pic_path;
			list($width,$height)=getimagesize($path);
			$data ['ratio']=$height/$width;
		
			unset ( $pic_path );
			$data_thread [] = $data;
		}
		$count = DB::fetch_first ( "SELECT count(*) as num  FROM %t as t right join %t as img  on t.tid=img.tid WHERE img.attachment !='' AND t.fid in(".$tids.")", $parameter);
		
		if($count['num']-($page * $limit) > 0)
		{
			$has_next =(int)1;
		}
		else
		{
			$has_next = (int)0;
		}
		
		$thread_info = array (
				"img_url" => DISCUZSERVERURL,
				"total_num" => (Int)$count['num'],
				"page" => (Int)$page,
				"has_next" => $has_next,
				'list' => $data_thread,
				'rs' => 1
		);
		return $thread_info;
			}
		}

?>