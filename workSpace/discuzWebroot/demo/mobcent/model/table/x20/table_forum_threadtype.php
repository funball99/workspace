<?php
	class table_forum_threadtype
	{
		public static function fetch_name_by_typeid($typeid)
		{
			$query = DB::query('SELECT name FROM '.DB::table('forum_threadtype').' WHERE typeid = '.$typeid);
			$post = DB::fetch($query);			
			return $post;
		}
	}
?>