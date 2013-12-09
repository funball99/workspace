<?php
	class table_forum_announcement
	{
		public static function fetch_all_by_displayorder()
		{
		return DB::query('SELECT * FROM '.DB::table('forum_announcement').' WHERE endtime = 0 OR endtime >= '.time().' ORDER BY displayorder, starttime DESC');
		}
	}
?>