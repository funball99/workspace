<?php 
class table_home_friend
{
	public function fetch_status_by_uid_followuid($uid,$followuid)
	{
		$query = DB::query('SELECT * FROM '.DB::table('home_friend').' WHERE (uid='.$uid.' AND fuid='.$followuid.')');
		while($rows = DB::fetch($query))
		{
			$arr[$rows['uid']] = $rows;
		}
		return $arr;
	}
	public function fetch_all_following_by_uid ( $uid, $start, $limit )
	{
		$query = DB::query('SELECT * FROM '.DB::table('home_friend').' WHERE uid='.$uid.' limit '.$start .','. $limit );
		while($rows = DB::fetch($query))
		{
			$arr[$rows['uid']] = $rows;
		}
		return $arr;
	}
	 public function count_follow_user ( $uid )
	 {
	 	$query = DB::query('SELECT count(*) as num FROM '.DB::table('home_friend').' WHERE uid='.$uid);
	 	$rows = DB::fetch($query);
	 	return $rows['num'];
	 }
	 public function get_status_firend_status($fuid){
	 	$fuid = implode(',',$fuid);
	 	$query = DB::query ( "SELECT uid,invisible as status FROM ".DB::table('common_session')." WHERE uid in(".$fuid.")");
	 	while($status = DB::fetch($query)){
	 		$friend_status[] = $status['uid'];
	 	}
	 	return $friend_status;
	 }
	 public function get_status_firend($fuid){
	 	$fuid = implode(',',$fuid);
	 	$query = DB::query ( "SELECT uid,invisible as status FROM ".DB::table('common_session')." WHERE uid in(".$fuid.")");
        while($status = DB::fetch($query)){
        	$friend_status[$status['uid']][] = $status;
        }
        return $friend_status;
	 }
}
?>