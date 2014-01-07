<?php
	class table_forum_threadclass
	{
		public static function fetch_all_by_typeid($typeid)
		{
			$query = DB::query('SELECT name FROM '.DB::table('forum_threadclass').' WHERE typeid = '.$typeid);
			$post = DB::fetch($query);			
			return $post;
		}
	}
?>