<?php
function fetch_all_by_tid_authorId($tableid, $tid, $authorID,$start, $end, $maxposition, $ordertype = 0) {
	$this=C::t('forum_post');
	$storeflag = false;
	if($this->_allowmem) {
		if($ordertype != 1 && $start == 1 && $maxposition > ($end - $start)) {
			$data = $this->fetch_cache($tid, $this->_pre_cache_key.'tid_');
			if($data && count($data) == ($end - $start)) {
				$delauthorid = $this->fetch_cache('delauthorid');
				$updatefid = $this->fetch_cache('updatefid');
				$delpid = $this->fetch_cache('delpid');
				foreach($data as $k => $post) {
					if(in_array($post['pid'], $delpid) || $post['invisible'] < 0 || in_array($post['authorid'], $delauthorid)) {
						$data[$k]['invisible'] = 0;
						$data[$k]['authorid'] = 0;
						$data[$k]['useip'] = '';
						$data[$k]['dateline'] = 0;
						$data[$k]['pid'] = 0;
						$data[$k]['message'] = lang('forum/misc', 'post_deleted');
					}
					if(isset($updatefid[$post['fid']]) && $updatefid[$post['fid']]['dateline'] > $post['dateline']) {
						$data[$k]['fid'] = $updatefid[$post['fid']]['fid'];
					}
				}
				return $data;
			}
			$storeflag = true;
		}
	}
	$data = DB::fetch_all('SELECT * FROM %t WHERE tid=%d AND authorid=$d AND position>=%d AND position<%d ORDER BY position'.($ordertype == 1 ? ' DESC' : ''), array(self::get_tablename($tableid), $tid,$authorID, $start, $end), 'pid');
	if($storeflag) {
		$this->store_cache($tid, $data, $this->_cache_ttl, $this->_pre_cache_key.'tid_');
	}
	return $data;
}