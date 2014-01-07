<?php 
class table_common_report{
	private $_table = 'common_report';
	public function fetch_by_urlkey($urlkey,$userId){
		$query = DB::query("SELECT id FROM ".DB::table($this->_table)." WHERE urlkey='".$urlkey."' AND uid=" . $userId);
        while($array = DB::fetch($query)){
        	return $array['id'];
        }
	}
    public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		return DB::insert($this->_table, $data, $return_insert_id, $replace, $silent);
	}
	public function update_num($id, $message) {
		$query = "UPDATE ".DB::table($this->_table)." SET message=CONCAT_WS('<br>', message,".$message."), num=num+1 WHERE id=".$id;
	    echo $query;
		DB::query("UPDATE ".DB::table($this->_table)." SET message=CONCAT_WS('<br>', message, ".$message."), num=num+1 WHERE id=".$id);
	}
}

?>