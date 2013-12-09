<?php
class table_home_blacklist
{
	protected $_table = 'home_blacklist';
	public function count_by_uid_buid($uid, $buid = 0) {
		$parameter = $this->_table;
		$wherearr = array();
		if($uid) {
			$wherearr[] = 'uid='.$uid;
		}
		if($buid) {
			$wherearr[] = "buid=".$buid;
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return DB::result_first("SELECT COUNT(*) FROM " .DB::table($parameter).$wheresql);
	}
	public function delete_by_uid_buid($uid, $buid){
		return DB::delete($this->_table,array('uid' =>$uid,'buid' => $buid));
	}
    public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		return DB::insert($this->_table, $data, $return_insert_id, $replace, $silent);
	}
}