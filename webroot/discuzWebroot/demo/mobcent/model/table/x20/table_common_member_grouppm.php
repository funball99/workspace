<?php 
	class table_common_member_grouppm
	{
		public function update_to_read_by_unread($uid, $gpmid) {
			return ($uid = dintval($uid)) && ($gpmid = dintval($gpmid, true)) ? DB::update('common_member_grouppm', array('gpmid' => $gpmid, 'status' => 0, "uid" => $uid ,"uid" => $uid),'status=1') : false;
		}
	}
?>