<?php
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../model/table_common_member_profile.php';
require_once '../../config/config_ucenter.php';
require_once '../Config/public.php';
require_once '../model/table_surround_user.php';
require_once '../tool/tool.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/Thumbnail.php';
require_once '../public/mobcentDatabase.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile ( 'function/forumlist' );
require_once ('./abstractSurroundTopicList.php');
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_common_member.php';
require_once '../model/table/x25/table_forum_typeoptionvar.php';
require_once '../model/table/x25/table_forum_threadtype.php';
require_once '../model/table/x25/table_forum_threadclass.php';

class surroundTopicListImpl_x25 extends abstractSurroundTopicList {
	function getSurroundTopicList() {
		$infoAccess = new mobcentGetInfo();
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
		$arrAccess = $infoAccess->sel_accessTopkent($accessSecret,$accessToken);
		$userId = $arrAccess['user_id'];
		$page = $_GET ['page'] ? $_GET ['page'] : 1;
		$limit = $_GET ['pageSize'] ? $_GET ['pageSize'] : 20; 
		$start_limit = $page==1?0:($page - 1) * $limit;
		
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
		
		$action = $_GET['poi'];
		switch($action)
		{
			case 'user':
				$count = surround_user::fetch_all_surrounding_user_count($userId,$longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum);
				$threadlist = surround_user::fetch_all_surrounding_user($userId,$longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum,$start_limit,$limit);
				foreach($threadlist as $key=>$val)
					{
						$status = DB::fetch_first ( "SELECT uid,invisible as status FROM ".DB::table('common_session')." WHERE uid=".$val['uid'] );
						if(empty($status))
						{
							$status['status']=1;
						}
						$data['pois']['distance'] = $val['distance'];
						$data['pois']['gender'] = memberProfile::fetch_gender_field_value('gender',$val['uid']);
						$data['pois']['icon'] = userIconImg($val['uid']);
							$query = DB::query( 'SELECT count(*) as num FROM %t WHERE buid=%d and uid =%d', array (
									'home_blacklist',
									$val['uid'],
									$userId
									
									
							) );
						while($value = DB::fetch($query)) {
							if($value['num'] != 0)
							{
								$isblack = 1;
							}
						$Is_friend=C::t('home_follow')->fetch_status_by_uid_followuid($userId,$val['uid']);
						if($Is_friend)
						{
							$data['pois']['is_friend'] = 1;
						}
						else
						{
							$data['pois']['is_friend'] = 0;
						}
						$data['pois']['is_black'] = $isblack;
						$data['pois']['location'] = $val['location'];
						$data['pois']['nickname'] = $val['username'];
						$data['pois']['status'] = empty($status['status'])?1:0;
						$data['pois']['uid'] = $val['uid'];
						$list[] = $data['pois'];
						$isblack = 0;
						}
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
				global $_G;
				$_G['groupid'] =$group['groupid'];
				$pic_path = '';
				$picNew = new topic();
				$tids =$infoAccess ->forum_display($fid,$picNew);
				
				$count = surround_user::fetch_all_surrounding_topic_count($longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum,$tids);
				$threadlist = surround_user::fetch_all_surrounding_topic($longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum,$start_limit,$limit,$tids);
				
				require_once libfile ( 'function/attachment' );
				$smile_query = DB::query("SELECT code FROM ".DB::table('common_smiley')." WHERE type = 'smiley'");
				while ($smile_list = DB::fetch($smile_query)) {
					$smile_arr[] = $smile_list;
				}
				foreach($smile_arr as $sr){
					$smiles[]=$sr[code];
				}
					foreach($threadlist as $key=>$val)
					{
						$topicid = $val['tid']; 
						if ($val ["attachment"] == 2) {
							$parameter = array (
									'forum_threadimage',
									( int ) $val ['tid']
							);
							$pic = DB::fetch_first ( "SELECT tid,attachment from %t where tid=%d", $parameter );
							
							$pic_path = $picNew->parseTargeImage($pic);
						}
						if ($pic_path && $pic){
							$data['pois'] ['pic_path'] = $pic_path;
						} else {
							$data['pois']['pic_path'] = '';
						}
		
						unset ( $pic_path );
						$data['pois']['topic_id'] = $val['tid'];
						$data['pois']['topicww'] = $val['typeid'];
						$data['pois']['location'] = $val['location'];
						$data['pois']['distance'] = $val['distance'];
						$data['pois']['board_id'] =( int ) $val['fid'];
						$data['pois']['board_name'] = surround_user::fetch_border_by_fid($val['fid']);
						$threadlist_info = surround_user::fetch_all_surrounding_topic_info($topicid);
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
							$data['pois']['type_id'] =( int ) $vals['typeid'];
							$data['pois']['sort_id'] = ( int )$vals['sortid'];
							$data['pois']['title'] = sub_str($vals["subject"], 0,40);
							$data['pois']['subject'] = $vals['message'];
							$data['pois']['essence'] = ( int ) $vals ['digest'] >0|| ( int ) $vals ['icon'] ==9 || (int ) $vals ['stamp'] ==0? 1 : 0; 
							$data['pois']['last_reply_date'] = ($vals['lastpost']). "000";
							$data['pois']['poll'] = $vals['lastpost']; 
							$data['pois']['top'] =( int ) $vals ['displayorder'] >0 || ( int ) $vals ['icon'] ==13 || (int ) $vals ['stamp'] ==4? 1 : 0;
						}
						$list[] = $data['pois'];
					}
					/*[fen lei ming cheng]*/
					for($i=0;$i<count($list);$i++){
						$fenlei_name= C::t ( 'forum_threadtype' )->fetch_name_by_typeid ($list[$i][sort_id]);
						foreach($fenlei_name as $fl){
							$fenleimingcheng=$fl['name'];
							$list[$i][title]="[".$fenleimingcheng."]".$list[$i][title];
						}
					}
					for($i=0;$i<count($list);$i++){
						$fenlei_type= C::t ( 'forum_threadclass' )->fetch_all_by_typeid ($list[$i][type_id]);
						foreach($fenlei_type as $ftype){
							$fenleitypemingcheng=$ftype['name'];
							$list[$i][title]="[".$fenleitypemingcheng."]".$list[$i][title];
						}
					}
					/*[end fen lei ming cheng]*/
					
					$info['pois'] = $list;
					$N = ceil ( $count / $limit );
					$info ['icon_url'] = ''; 
					$info ['page'] = (Int)$page;
					$info ['rs'] = (int)1;
					$info ['has_next'] = ($page >= $N || $N == 1) ? 0 : 1; 
					return $info;
			}
		}

}

?>