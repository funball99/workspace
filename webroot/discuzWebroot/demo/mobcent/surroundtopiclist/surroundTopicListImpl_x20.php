<?php
define ( 'IN_MOBCENT', 1 );
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../Config/public.php';
require_once '../tool/tool.php';
require_once '../../config/config_ucenter.php';
require_once '../public/common_json.php';
require_once '../tool/Thumbnail.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once libfile ( 'function/forumlist' );
require_once ('./abstractSurroundTopicList.php');
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/mobcentDatabase.php';
require_once '../model/table/x20/table_common_member.php';
require_once libfile ( 'function/discuzcode' );
class surroundTopicListImpl_x20 extends abstractSurroundTopicList {
	function getSurroundTopicList() {
		$infoAccess = new mobcentGetInfo();
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20; 
		$start_limit = $page==1?0:($page - 1) * $limit - 1;  
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
		$group = $infoAccess->rank_check_allow($accessSecret,$accessToken,$qquser);
		if(!$group['allowvisit'])
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '01110001';
			return $data_post;
			exit();
		}
		setglobal('groupid', $group['groupid']);
		global $_G;
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$userId = isset($arrAccess['user_id'])?$arrAccess['user_id']:0;
 
		$longitude =$_GET['longitude'];
		$latitude =	$_GET['latitude'];	
		$radius =isset($_GET['radius']) ? $_GET['radius']:100000;
		
		define('EARTH_RADIUS',6378137);
		define('PI',pi());
		$lngRange = $radius * 180 / (EARTH_RADIUS * PI);
		$latRange = $lngRange /cos($latitude * PI / 180); 
		$latpoor = $latitude-$latRange;
		$latsum = $latitude+$latRange;
		$longpoor = $longitude-$lngRange;
		$longsum = $longitude+$lngRange;
		require_once libfile ( 'function/attachment' );
		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$action = $_GET['poi']; 
		try {
			switch($action)
			{
				case 'user':
					$count = C::t('home_surrounding_user') ->fetch_all_surrounding_user_count($userId,$longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum);
					$threadlist = C::t('home_surrounding_user') ->fetch_all_surrounding_user($userId,$longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum,$start_limit,$limit);
					foreach($threadlist as $key=>$val)
						{
							$status = DB::fetch_first ( "SELECT uid,invisible as status FROM ".DB::table('common_session')." WHERE uid=".$val['uid'] );
							if(empty($status))
							{
								$status['status']=1;
							}
							$data['pois']['distance'] = $val['distance'];
							$data['pois']['gender'] = C::t('common_member_profile') -> fetch_gender_field_value('gender',$val['uid']);
							$data['pois']['icon'] = userIconImg($val['uid']);
							
							if (C::t ( 'home_blacklist' )->count_by_uid_buid ( $userId )) {
								$query = DB::fetch_first( 'SELECT count(*) as num FROM '.DB::table('home_blacklist').' WHERE buid='.$userId.' and uid ='.$val['uid'] );
								if($query['num'] != 0)
								{
									$data['pois']['is_black'] = 1;
								}
								else
								{
									$data['pois']['is_black'] = 0;
								}
							}
							$Is_friend=C::t('home_friend')->fetch_status_by_uid_followuid($userId,$val['uid']);
							if($Is_friend)
							{
								$data['pois']['is_friend'] = 1;
							}
							else
							{
								$data['pois']['is_friend'] = 0;
							}
		
							$data['pois']['location'] = $val['location'];
							$data['pois']['nickname'] = $val['username'];
							$data['pois']['status'] = empty($status['status'])?1:0;
							$data['pois']['uid'] = $val['uid'];
							$list[] = $data['pois'];
							unset($status);
						}
						$info['pois'] = $list;
						$N = ceil ( $count / $limit );
						$info ['icon_url'] = ''; 
						$info ['page'] = $page;
						$info ['rs'] = (int)1;
						$info ['has_next'] = ($page >= $N || $N == 1) ? 0 : 1;  				
						return $info;
					
					break;
				case 'topic':
					$picNew = new topic();
					$tids = $infoAccess->forum_check_content($uid,$picNew);
					$count = C::t('home_surrounding_user') ->fetch_all_surrounding_topic_count($longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum,$tids);
					$threadlist = C::t('home_surrounding_user') ->fetch_all_surrounding_topic($longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum,$start_limit,$limit,$tids);
					$smile_query = DB::query("SELECT code FROM ".DB::table('common_smiley')." WHERE type = 'smiley'");
					while ($smile_list = DB::fetch($smile_query)) {
						$smile_arr[] = $smile_list;
					}
					foreach($smile_arr as $sr){
						$smiles[]=$sr[code];
					}
					foreach($threadlist as $key=>$val)
						{
							if($val['attachment'] == 2)
							{
								$pic = C::t('forum_thread') ->fetch_all_threadimage($val);
								
								$pic_path = $picNew->parseTargeImage($pic);
							}
							else {
								$pic_path ='';
							}
							$topicid = $val['tid'];
							$data['pois']['topic_id'] = $val['tid'];
		
							$data['pois']['location'] = $val['location'];
							$data['pois']['distance'] = $val['distance'];
							$data['pois']['top'] =( int ) $val['first'];
							$data['pois']['board_id'] =( int ) $val['fid'];
							$data['pois']['board_name'] = C::t('home_surrounding_user') ->fetch_border_by_fid($val['fid']); 
							$threadlist_info = C::t('home_surrounding_user') ->fetch_all_surrounding_topic_info($topicid);
							foreach($threadlist_info as $key=>$thread){
								$message_query=DB::fetch(DB::query("SELECT message FROM ".DB::table('forum_post')." WHERE first=1 AND tid=".(Int)$thread['tid']));
								preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i",  $message_query['message'] ,$matches);
								$patten  = array("\r\n", "\n", "\r");
								$data_subject = str_replace($matches[1], '', $message_query ['message']);
								$data_subject =str_replace($patten, '', $data_subject);
								$data_subject = preg_replace("#(\w*)\[.*?\](\w*)#","$1$2",$data_subject);
								foreach($smiles as $si){
									$data_subject =str_replace($si, '', $data_subject);
								}
								$data_subject =trim($data_subject);
								$data_subject = sub_str($data_subject, 0,40);
								$threadlist_info[$key][message]=$data_subject;
							}
							foreach($threadlist_info as $key=>$vals)
							{
								$data['pois']['replies'] = $vals['replies'];
								$data['pois']['hits'] = ( int )$vals['views'];
								if($vals ["special"]==1){
									$data ['pois']['vote'] = (int)1;
								}else{
									$data ['pois']['vote'] = (int)0;
								}
								$data['pois']['user_nick_name'] = $vals['author'];
								$data['pois']['hot'] = ( int ) $vals ['stamp'] ==1|| ( int ) $vals ['icon'] ==10? 1 : 0;
								$length = strlen($vals["subject"]);
								if ( $length > 26){
									$data['pois']['title'] = sub_str($vals["subject"], UC_DBCHARSET);
								}else{
									$data['pois']['title'] = $vals["subject"];
								}
								$data['pois']['subject'] = $vals['message'];
								$data['pois']['essence'] = ( int ) $vals ['digest'] >0|| ( int ) $vals ['icon'] ==9 || (int ) $vals ['stamp'] ==0? 1 : 0; 
								$data['pois']['last_reply_date'] = ($vals['lastpost']). "000";
								$data['pois']['poll'] = $vals['lastpost'];
								$data ['pois']['pic_path'] = $pic_path;
								$data['pois']['top'] =( int ) $vals ['displayorder'] >0 || ( int ) $vals ['icon'] ==13 || (int ) $vals ['stamp'] ==4? 1 : 0;
								unset ( $pic_path );
							}
							$list[] = $data['pois'];
						}
		
						$info['pois'] = $list;
						$N = ceil ( $count / $limit );
						$info ['icon_url'] = '';
						$info ['page'] = (Int)$page;
						$info ['rs'] = (int)1;
						$info ['has_next'] = ($page >= $N || $N == 1) ? 0 : 1;
						return $info;
					
			}
		
		}catch (Exception $e)
		{
			$data_notice ['rs'] = 0;
			$data_notice ['error'] = 9999;
			echo json_encode ( $data_notice );
		}
		}

}

?>