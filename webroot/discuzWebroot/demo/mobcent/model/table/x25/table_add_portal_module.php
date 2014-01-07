<?php
class add_portal_module{
	static public function check_module_isimage($isimage){
		$str = isset($isimage)?'isimage = '.$isimage:'';
		return DB::fetch_all(" SELECT * FROM %t where $str  ORDER BY display desc limit 0,5",array('add_portal_module'));
	}
	static public function check_module_isimage_count($isimage){
		$str = isset($isimage)?'isimage = '.$isimage:'';
		return DB::fetch_all(" SELECT count(*) as num FROM %t where $str  ",array('add_portal_module'));
	}
	static public function check_module(){
		return DB::fetch_all(" SELECT * FROM %t ORDER BY display DESC limit 0,6",array('add_module'));
	}
	static public function check_module_daohang(){
		return DB::fetch_all(" SELECT * FROM %t ORDER BY display DESC limit 1",array('add_module'));
	}
	static public function check_module_daohang_info($id){
		return DB::fetch_all(" SELECT * FROM %t where id=%d limit 1",array('add_module',$id));
	}
	static public function check_module_list($id,$start,$limit){
		return DB::fetch_all(" SELECT * FROM %t where mid=%d AND isimage !=1  ORDER BY time DESC limit %d,%d",array('add_portal_module',$id,$start,$limit));
	}
	
	static public function check_module_list_count($id){
		return DB::fetch_first(" SELECT count(*) as num FROM %t where mid=%d AND isimage !=1 ",array('add_portal_module',$id));
	}
	static public function check_module_sum(){
		return DB::fetch_all(" SELECT count(*) num FROM %t ",array('add_module'));
	}
	static public function check_module_edit($id){
		return DB::fetch_all(" SELECT * FROM %t WHERE id =%d ",array('add_module',$id));
	}
	static public function check_module_isimage_Edit($id){
		return DB::fetch_all(" SELECT * FROM %t where isimage=1 AND id=%d",array('add_portal_module',$id));
	}
}