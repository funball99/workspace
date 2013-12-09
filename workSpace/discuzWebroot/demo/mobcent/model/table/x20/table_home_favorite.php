<?php 
	class table_home_favorite
	{
	public function fetch_by_id_idtype($id, $idtype, $uid = 0) {
		if($uid) {
			$uidsql = ' AND uid='.$uid;
		}
		else
		{
			$uidsql = ' AND uid=0';
		}
		return DB::fetch_first("SELECT * FROM ".DB::table('home_favorite')." WHERE id=".$id." AND idtype='".$idtype."' $uidsql");
	}
	public function count_by_uid_idtype($id, $idtype)
	{
		return DB::fetch_first("SELECT count(favid) as num FROM ".DB::table('home_favorite')." WHERE uid=".$id." AND idtype='".$idtype."'");
	}
	public function count_by_id_idtype($id, $idtype,$favid = 0)
	{
		$array = DB::fetch_first("SELECT count(favid) as num FROM ".DB::table('home_favorite')." WHERE favid = ".$favid." AND uid=".$id." AND idtype='".$idtype."'");
		return $array['num'];
	}
	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		return DB::insert('home_favorite', $data, $return_insert_id, $replace, $silent);
	}
	public function delete($favid){
		return DB::delete(('home_favorite'),array('favid'=>$favid));
	}
	}
?>