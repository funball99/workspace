<?php
 
class table_home_surrounding_user
{
	private static $_table='home_surrounding_user';
	private static $_topic='forum_post';
	private static $_thread='forum_thread';
	private static $_forum = 'forum_forum';
	public function __construct()
	{
		$parameter = array (
				'common_member',
				'home_surrounding_user'
		);

	}
	 
	public static function fetch_num_by_userid($userid)
	{
		$data=DB::fetch_first("SELECT count(*) as num FROM ".DB::table('home_surrounding_user')." WHERE object_id= ".$userid." AND type=1");
		return $data['num'];
	}
	 
	public static function fetch_update_by_userid($longitude,$latitude,$location,$userId)
	{
		$data = DB::update(self::$_table, array('longitude'=>$longitude,'latitude'=>$latitude,'location'=>$location), array('object_id'=>$userId,'type'=>1));
		return $data;
	}
	 
	public static function fetch_insert_by_userid($longitude,$latitude,$location,$userId)
	{
		$data = DB::insert(self::$_table, array('longitude'=>$longitude,'latitude'=>$latitude,'location'=>$location,'object_id'=>$userId,'type'=>1), false, true);
		return $data;
	}
	 
	public static function fetch_all_surrounding_user($userId,$longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum,$start_limit,$limit)
	{
		$query = DB::query('SELECT user.*,poi_id,longitude,latitude,SQRT(POW(('.$longitude.'-longitude)/0.012*1023,2)+POW(('.$latitude.'-latitude)/0.009*1001,2)) AS distance,location FROM '.DB::table('common_member').' as user,'.DB::table('home_surrounding_user').' as poi WHERE user.uid=poi.object_id AND type= 1 AND  poi.object_id<> '.$userId.' AND longitude between '.$longpoor.'  AND '.$longsum.'  AND latitude between '.$latpoor.'  AND '.$latsum.'    ORDER BY distance limit '.$start_limit.','.$limit);
		while($rows = DB::fetch($query))
		{
			$data[$rows['uid']] =$rows;
		}
		return $data;
		
	}
	public static function fetch_all_surrounding_user_count($userId,$longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum)
	{
		$query = DB::query("SELECT count(user.uid) as num FROM ".DB::table('common_member')." as user,".DB::table('home_surrounding_user')." as poi WHERE user.uid=poi.object_id AND type= 1 AND  poi.object_id<> ".$userId." AND longitude between '".$longpoor."'  AND '".$longsum."'  AND latitude between '".$latpoor."'  AND '".$latsum."'",array($longitude,$latitude,$userId));
		$data = DB::fetch($query);
		return $data['num'];
	
	}
	 
	public static function insert_all_apply_location($longitude,$latitude,$location,$pid)
	{
		$data = DB::insert('home_surrounding_user', array(
				'poi_id' => '',
				'longitude' => $longitude,
				'latitude' => $latitude,
				'object_id' => $pid,
				'type' => 2,
				'location' => $location,
		));
		return $data;
	}
	 
	public static function insert_all_thread_location($longitude,$latitude,$location,$pid)
	{
		$data = DB::insert('home_surrounding_user', array(
		'poi_id' => '',
		'longitude' => $longitude,
		'latitude' => $latitude,
		'object_id' => $pid,
		'type' => 3,
		'location' => $location,
		));
		return $data;
	}
	 
	public static function fetch_all_by_pid($pid)
	{
		$query = DB::query('SELECT location FROM '.DB::table('home_surrounding_user').' WHERE type != 1 and object_id = '.$pid);
		$data = DB::fetch($query);
		return $data;
	}
	 
	public static function fetch_all_surrounding_topic_count($longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum,$tids)
	{
		
		$query = DB::query("SELECT count(*) as num FROM ".DB::table('home_surrounding_user')." as pio ,".DB::table('forum_post')." topic  WHERE topic.pid=pio.object_id  AND topic.invisible > '-1'  AND pio.type=3 AND pio.longitude between $longpoor AND $longsum  AND pio.latitude between $latpoor  AND $latsum AND topic.fid in(".$tids.") ORDER BY topic.dateline desc ");
		$data = DB::fetch($query);
		return $data['num'];
	}
	 
	public function forum_surround_display()
	{
		$Tidquery =DB::query("SELECT t.tid FROM ".DB::table('forum_post')." t,".DB::table('forum_forum')." f WHERE t.fid=f.fid AND f.status>0");
		while($dataForum = DB::fetch($Tidquery))
		{
			$tids[]=$dataForum['tid'];
		}
		$tids = implode(',', $tids);
		$tids =empty($tids)?0:$tids;
		return $tids;
	}
	public static function fetch_all_surrounding_topic($longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum,$start_limit,$limit,$tids)
	{
		$query = DB::query("SELECT topic.pid,topic.tid,topic.fid,topic.first,topic.attachment,longitude,latitude,SQRT(POW(($longitude-longitude)/0.012*1023,2)+POW(($latitude-latitude)/0.009*1001,2)) AS distance,location FROM ".DB::table('home_surrounding_user')." as pio ,".DB::table('forum_post')." topic  WHERE topic.pid=pio.object_id  AND topic.invisible > '-1'  AND pio.type=3 AND pio.longitude between $longpoor AND $longsum  AND pio.latitude between $latpoor  AND $latsum AND topic.fid in(".$tids.") ORDER BY topic.dateline desc limit $start_limit,$limit");
		while($rows = DB::fetch($query))
		{
			$data[] = $rows;
		}
		return $data;
	}
	public static function fetch_all_surrounding_topic_info($topicid)
	{
		$query = DB::query('SELECT * FROM '.DB::table('forum_thread').' WHERE tid = '.$topicid);
		while($rows = DB::fetch($query))
		{
			$data[] = $rows;
		}
		return $data;
	
		
	}
	public static function fetch_border_by_fid($fid)
	{
		$query = DB::query("SELECT name FROM ".DB::table('forum_forum')." WHERE fid = ".$fid);
		$data = DB::fetch($query);
		return $data['name'];
	}
}