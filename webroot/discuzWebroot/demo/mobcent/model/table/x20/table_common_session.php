<?php
class table_common_session
{
	public function fetch_all_by_uid($uids, $start = 0, $limit = 0) {
		$str = '';
		foreach($uids as $value){
		    $str .= $value.",";
		}
		$str = substr($str,0,-1);	
		$data = array();
		if(!empty($uids)) {
			$query = DB::query('SELECT * FROM '.DB::table('common_session').' WHERE uid in ('.$str.') limit '.$start.','. $limit);
			while($arr = DB::fetch($query))
			{
				$data[$arr['uid']] = $arr;
			}
		}
		return $data;
	}
	
}