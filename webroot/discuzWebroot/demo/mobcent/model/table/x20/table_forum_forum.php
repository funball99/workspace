<?php 
class table_forum_forum
{
	public function fetch_all_info_by_fids($fids, $status = 0, $limit = 0, $fup = 0, $displayorder = 0, $onlyforum = 0, $noredirect = 0, $type = '', $start = 0) {
		$sql = $fids ? "f.fid =".$fids : '';
		$sql .= empty($fup) ? '' : ($sql ? ' AND ' : '').'f.fup ='."$fup";
		if(!strcmp($status, 'available')) {
			$sql .= ($sql ? ' AND ' : '')." f.status>'0'";
		} elseif($status) {
			$sql .= $status ? ($sql ? ' AND ' : '')." f.status=".$status : '';
		}
		$sql .= $onlyforum ? ($sql ? ' AND ' : '').'f.type<>\'group\'' : '';
		$sql .= $type ? ($sql ? ' AND ' : '').'f.type='."'$type'" : '';
		$sql .= $noredirect ? ($sql ? ' AND ' : '').'ff.redirect=\'\'' : '';
		$ordersql = $displayorder ? ' ORDER BY f.displayorder' : '';
		$limitsql = $limit ? 'limit '.$start.','.$limit : '';
		if(!$sql) {
			return array();
		}
		return DB::query("SELECT ff.*, f.* FROM ".DB::table('forum_forum')." f LEFT JOIN ".DB::table('forum_forumfield')."  ff USING (fid) WHERE ".$sql.$ordersql.$limitsql);
	}
	public function fetch_all_subforum_by_fup($fups) {
		return DB::fetch("SELECT fid, fup, name, threads, posts, todayposts, domain FROM ".DB::table('forum_forum')." WHERE status='1' AND fup IN (".$fups.") AND type='sub' ORDER BY displayorder");
	}
	public function fetch_info_by_fid($SubId)
	{
			return DB::fetch_first("SELECT ff.*, f.* FROM ".DB::table('forum_forum')." f LEFT JOIN ".DB::table('forum_forumfield')." ff ON ff.fid=f.fid WHERE f.fid=".$SubId);
	}
	public function fetch_all_name_by_fid($fid)
	{
		return DB::fetch_first("SELECT name,allowpostspecial FROM ".DB::table('forum_forum')." WHERE fid =".$fid);
	}
}
?>