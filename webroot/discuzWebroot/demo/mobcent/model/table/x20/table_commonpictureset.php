<?php
 
class table_commonpictureset
{
	public function  __construct()
	{
		$this->_table = 'forum_thread';
		$table = 'forum_threadimage';

	}
	public static function fetch_all_thread_img($start_limit, $limit,$tids)
	{
		$status =-1;
		$query = DB::query ( "SELECT * FROM ".DB::table('forum_thread')." as t right join ".DB::table('forum_threadimage')." as img  on t.tid=img.tid WHERE img.attachment !=''  AND t.displayorder > ".$status." and t.fid in(".$tids.") ORDER BY t.dateline DESC limit " .$start_limit .','. $limit);
		while($rows = DB::fetch($query))
		{
			$data[] =$rows;
		}
		return $data;
	}
}