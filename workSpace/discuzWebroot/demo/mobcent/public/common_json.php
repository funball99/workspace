<?php
require_once '../Config/public.php';
class common_json
{
	private static $_instance = '';
	private static $_username = '';
	private static $_json = '';
	private function __construct()
	{
	
	}
	
	private  function  __clone()
	{
		trigger_error('Clone is not allow', E_USER_ERROR);
	}
	public static function on_error_content($error_content){
		$data_notice['rs'] = 0;
		$data_notice['error'] = Common::get_unicode_charset($error_content);
		echo json_encode($data_notice);
	}
	public static function on_success(){
		$data_notice['rs'] = 1;
		return json_encode($data_notice);
	}
	public static function on_fail(){
		$data_notice['rs'] = 0;
		return json_encode($data_notice);
	}
	 
	public function on_error()
	{
		$data_notice ['rs'] = 0;
		$data_notice ['error'] = '9999';
		return json_encode($data_notice);
	}
	public static function getInstance()
	{
		if(!self::$_instance instanceof  self)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	public static function on_postsNotices($list)
	{
		$data_notice ['rs'] = 1;  
		foreach ( $list as $k => $v ) {
			preg_match ( '#.*ptid\=(.+).*\&pid\=(.+)\".*\"_blank\">([^<]+)<\/a>#i', $v ['note'], $array );
			$list_sub ['reply_remind_id'] = ( int ) $v ['id']; 
			$list_sub ['topic_subject'] = $array ['3'];  
			$list_sub ['reply_posts_id'] = ( int ) $array ['2'];  
			$list_sub ['topic_id'] = ( int ) $array ['1'];  
			$list_sub ['reply_nick_name'] = $v ['author'];  
			$list_sub ['user_id'] = ( int ) $v ['authorid'];  
			$list_sub ['is_read'] = ( int ) 1 - $v ['new']; 
			$list_sub ['replied_date'] = $v ['dateline'] . '000';  
			$list_sub ['create_time'] = $v ['dateline'] . '000';  
			$list_sub ['icon'] = userIconImg ( $v ['authorid'] );
			$data_notice ['list'] [] = $list_sub;
		}
		$data_notice ['icon_url'] = DISCUZSERVERURL; 
		return echo_json($data_notice);
	}
	public static function on_updateRead()
	{
		$data_notice ['rs'] = 1; 
		self::$_json = $data_notice;
		return self::$_json;
	}
	public static function on_myFavorites($fav,$page,$num)
	{
		foreach($fav as $k=>$val){
			$data['id']			=(int)$val["favid"];
			$data['topic_id']	=(int)$val["id"];
			$data['title']		=$val["title"];
			$data['user_nick_name']	=$val["username"].'('.$val["description"].')'; 
			$data_notice['list'][]=$data;
		}
		$data_notice['rs'] = 1;
		$data_notice['page'] = $page;
		$data_notice['has_next'] = ($page>=$num || $num==1) ?0:1;  
		self::$_json = $data_notice;
		return self::$_json;
	}
	public static function on_getRecommendUsers($uid,$list,$member_status,$data_rc,$page,$num)
	{
		
		foreach ( $list as $k => $v ) {
			foreach($data_rc as $key =>$val)
			{
				if(array_key_exists($v ['uid'], $val))
				{
					$data_rc ['list'] [$v ['uid']] ['gender'] = $v ['gender'];
					$data_rc ['list'] [$v ['uid']] ['status'] = $member_status [$v ['uid']];
					$data_rc ['list'] [$v['uid']]['level'] =(int) $v['stars'];
					$data2 [] = $data_rc ['list'] [$v ['uid']];
				}
			}
			
		}
		$data_notice ['list'] = $data2;
		$data_notice ['icon_url'] = '';  
		$data_notice ['page'] = $page;
		$data_notice ['has_next'] = ($page >= $num || $num == 1) ? 0 : 1;  
		$data_notice ['rs'] = 1;
		self::$_json = $data_notice;
		return self::$_json;
	}
	public static function on_CommonPictureSet($count,$page,$has_next,$data_thread)
	{
		$data_notice = array (
				"img_url" => DISCUZSERVERURL,
				"total_num" => $count['num'],
				"page" => $page,
				"has_next" => $has_next,
				'list' => $data_thread,
				'rs' => 1
		);
		return echo_json($data_notice);
	} 
	public static function on_userTopicList($Config,$count,$page,$data_thread)
	{
		$data_notice = array (
				"img_url" => $Config ['pic_path'],
				"total_num" => ( int ) $count,
				"page" => $page,
				'list' => $data_thread,
				"rs" => 1
		);
		return echo_json($data_notice);
	}
	
	public static function on_userReplyList($Config,$count,$page,$data_post)
	{
		$thread_info = array (
			"img_url" => $Config ['pic_path'],
			"total_num" => $count,
			"page" => $page,
			"has_next" => ($page >= $N || $N == 1) ? 0 : 1,
			'list' => $data_post,
			"rs" => 1
	);
		return echo_json($thread_info);
	}
}