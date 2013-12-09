<?php
class table_ucenter_pm_members
{
	public function __construct() {
	
		$this->_table = 'ucenter_pm_members';
		$this->_pk    = 'id';
	
		parent::__construct();
	}
	
	public static function isreadstatus($uid,$plid)
	{
		$sql = 'UPDATE %t SET isnew = 0 WHERE plid =%d AND uid =%d AND isnew = 1';
		$arr = array('ucenter_pm_members', $plid,$uid);
		return DB::query($sql,$arr);
	}
}