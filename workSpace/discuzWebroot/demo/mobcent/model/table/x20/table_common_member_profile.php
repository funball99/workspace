<?php
class table_common_member_profile
{
	private $_fields;
	public function __construct() {
		$this->_table = 'common_member_profile';
		
	}
	public function get_profile_by_uid($uids, $file) {
		$uidss = implode ( ',', $uids );
		$parameter = array (
				$this->_table,
				'common_member',
				'common_usergroup'
		);
		if ($uidss){
			$where = ' AND a.uid in(' . $uidss . ')  ';
		}
		$query = DB::query( "SELECT c.stars,b.groupid,b.username,a.uid," . $file . " FROM ".DB::table('common_member_profile')."  AS a left join ".DB::table('common_member')." AS b ON a.uid=b.uid left join ".DB::table('common_usergroup')." AS c ON b.groupid=c.groupid WHERE 1=1 " . $where . 'ORDER BY c.stars DESC');
		while($rows = DB::fetch($query))
		{
			$arr[$rows['uid']] = $rows;
		}
		return $arr;
	}
	public static function fetch_gender_field_value($field,$userId) {
		$query = DB::query ('SELECT '.$field.' as gender FROM '.DB::table('common_member_profile').' WHERE uid='.$userId.' limit 1');
		$data = DB::fetch($query);
		return $data['gender'];		
	}
	public static function get_online_member_count($todaytime,$tomorrowtime){
		$sql = 'SELECT COUNT(*) FROM %t where lastolupdate between %s and %s';
		$arr =  array('common_session',$todaytime,$tomorrowtime);
		return DB::result_first($sql,$arr);
	}
}
?>