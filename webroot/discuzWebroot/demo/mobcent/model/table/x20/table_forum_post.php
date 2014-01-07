<?php
class table_forum_post
{
	private static $tablename = 'forum_post';
	private static $_pk = 'pid';
	public function __construct() {
	
	}
	

	public static function fetch_all_by_authorid($tableid, $authorid, $outmsg = true, $order = '', $start = 0, $limit = 0, $first = null, $invisible = null, $fid = null, $filterfid = null)
	{
		$query = DB::query ( 'SELECT a.pid,b.tid,b.attachment,b.subject,b.special FROM '.DB::table('forum_post').'  a INNER JOIN '.DB::table('forum_thread').' b ON a.tid = b.tid WHERE a.authorid='.$authorid.' AND a.first=0 GROUP BY b.tid '  . ($order ? 'ORDER BY a.dateline ' . $order : '')  .' limit '.$start.','.$limit);
		while($post = DB::fetch($query)) {
			if(!$outmsg) {
				unset($post['message']);
			}
			$postlist[$post[self::$_pk]] = $post;
		}
		return $postlist;
	}
	public static function count_by_authorid($authorid)
	{
		$query = DB::query('SELECT count(*) as num FROM '.DB::table('forum_post').' WHERE authorid = '.$authorid);
		$count = DB::fetch($query);
		return $count['num'];
	}
	public function count_by_tid_dateline($tableid, $tid, $dateline) {
		return DB::result_first('SELECT COUNT(*) FROM '.DB::table('forum_post').' WHERE tid='.$tid.' AND invisible=0 AND dateline<='.$dateline);
	}
	public function count_by_tid_invisible_authorid($tid,$authorid){
		DB::result_first("SELECT COUNT(*) FROM ".DB::table('forum_post')." WHERE tid='$tid' AND invisible='0' AND authorid='$authorid'");
	}
	public function fetch_all_by_tid_range_position($tid, $start, $end, $maxposition)
	{
		$q2 = DB::query("SELECT pid, position FROM ".DB::table('forum_postposition')." WHERE tid='$tid' AND position>='$start' AND position<'$end' ORDER BY position");
		while ($post = DB::fetch($q2)) {
			$data[$post['pid']] = $post;
		}
		return $data;
	}
	public function fetch_threadpost_by_tid_invisible($tid, $invisible = null) {
		$query = DB::query('SELECT * FROM '.DB::table('forum_post').' WHERE tid='.$tid.' AND first=1');
		while ($post = DB::fetch($query)) {
			$data[$post['pid']] = $post;
				
		}
		return $data;
	}
	public function fetch_all_common_viewthread_by_tid($tid, $userId, $pagebydesc,  $limit, $start_limit, $pagebydesc)
	{
		$query =  DB::query("SELECT p.*  FROM ".DB::table('forum_post')." p  WHERE p.tid=".$tid." AND p.invisible='0'  AND p.authorid=".$userId." ORDER BY p.dateline LIMIT  ".$start_limit.",". $limit);
		while ($post = DB::fetch($query)) {
			$data[$post['pid']] = $post;
			
		}
		return $data;
	}
	public function fetch_all_common_viewthread_by_tid_count($tid, $userId, $pagebydesc, $pagebydesc)
	{
		$query =  DB::query("SELECT count(*) as num  FROM ".DB::table('forum_post')." p  WHERE p.tid=".$tid." AND p.invisible='0'  AND p.authorid=".$userId." ORDER BY p.dateline");
		while ($post = DB::fetch($query)) {
			$data['num'] = $post['num'];
				
		}
		return $data;
	}
	public function fetch_all($tid,$tradespids)
	{
		$query =  DB::query("SELECT p.*  FROM ".DB::table('forum_post')." p  WHERE p.tid=".$tid." AND p.pid=$tradespids ");
		while ($post = DB::fetch($query)) {
			$data[$post['pid']] = $post;
				
		}
		return $data;
	}
	
}