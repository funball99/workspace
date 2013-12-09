<?php 
class table_common_member_count{
	private $_table = 'common_member_count';
	public function increase($uids, $creditarr) {
		$sql = array();
		$allowkey = array('extcredits1', 'extcredits2', 'extcredits3', 'extcredits4', 'extcredits5', 'extcredits6', 'extcredits7', 'extcredits8',
				'friends', 'posts',	'threads', 'oltime', 'digestposts', 'doings', 'blogs', 'albums', 'sharings', 'attachsize', 'views',
				'todayattachs', 'todayattachsize');
		foreach($creditarr as $key => $value) {
			if(($value = intval($value)) && $value && in_array($key, $allowkey)) {
				$sql[] = "`$key`=`$key`+'$value'";
			}
		}
		if(!empty($sql)){
			DB::query("UPDATE ".DB::table($this->_table)." SET ".implode(',', $sql)." WHERE uid IN (".dimplode($uids).")", 'UNBUFFERED');
		}
	}
	
}

?>