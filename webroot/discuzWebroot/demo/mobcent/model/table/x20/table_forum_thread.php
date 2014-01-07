<?php 
	class table_forum_thread
	{

		function digest($uid,$digest,$start = 0, $limit = 0, $tableid = 0,$digestglue = '>=', $displayorder = 0, $glue = '>=') {
			$thread_obj = C::t('forum_thread');
			$parameter = array($thread_obj->get_table_name($tableid), $digest, $displayorder);
			$digestglue = helper_util::check_glue($digestglue);
			$glue = helper_util::check_glue($glue);
			if($uid) $where='authorid='.$uid.' AND ';
			
			$data = DB::fetch_all("SELECT * FROM %t WHERE ".$where." digest{$digestglue}%d AND displayorder{$glue}%d".DB::limit($start, $limit), $parameter, 'tid');
			$data[count] = DB::result_first("SELECT count(*) FROM %t WHERE ".$where." digest{$digestglue}%d AND displayorder{$glue}%d", $parameter, 'tid');
			return $data;
		}
		
	 
		function get_subject_by_tid($tid){
			$sql = 'select subject from '.DB::table('forum_thread').' where tid='.$tid;
			$data = DB::result_first($sql);
			return $data;
		}
		function get_special_by_tid($tid){
			$sql = 'select special from '.DB::table('forum_thread').' where tid='.$tid;
			$data = DB::result_first($sql);
			return $data;
		}
		public function fetch_name_by_fid($gid)
		{
			$sql = DB::query('select name from '.DB::table('forum_forum').' where fid='.$gid);
			$data = DB::fetch($sql);
			return $data;
		}
		 
		function update_thread_pos($postionid,$author,$tid){
			$sql = 'UPDATE %t SET maxposition=%d,lastposter=%s,replies=replies+1,lastpost=%d WHERE tid=%d';
			$array1 = array('forum_thread',$postionid,$author,time(),$tid);
			return DB::query($sql,$array1);
		}
		 
		function get_forum_attachment_unused($val){
			$sql = "SELECT * FROM %t WHERE aid=%d";
			$array1 = array('forum_attachment_unused',$val);
			return  DB::query ($sql , $array1);
		}
		
		 
		function update_forum_attachment($tid,$attachtableid,$uid,$pid,$aids){
			$sql = "UPDATE %t SET tid=%d,tableid=%d,uid=%d,pid=%d WHERE aid IN (%n)";
			$array1 = array ('forum_attachment',$tid,$attachtableid,$uid,$pid,$aids);
			return DB::query($sql,$array1);
		}
		function get_forum_post_by_pid($toReplyId){
			$sql = "SELECT * FROM ".DB::table('forum_post')." WHERE pid=".$toReplyId." limit 1";
			return DB::fetch_first ($sql);
		}
		 
		function get_hot_invitation($start_limit,$limit){
			$sql = "SELECT * FROM %t as t right join %t as img  on t.tid=img.tid WHERE img.attachment !='' ";
		    $sql .= "DB::limit ( $start_limit, $limit )";
		    $arr = array ('forum_thread','forum_threadimage');
			return  DB::fetch_all ($sql,$arr,'tid');
		}
		 
		function get_thread_count(){
			$sql =  "SELECT count(*) as num  FROM %t as t right join %t as img  on t.tid=img.tid WHERE img.attachment !='' ";
			$arr = array ('forum_thread','forum_threadimage');
			return DB::fetch_first($sql,$arr);	
		}
		public function fetch_all_by_authorid_displayorder($uid,$start,$limit)
		{
			$query = DB::query("SELECT t.* FROM ".DB::table('forum_thread')." t WHERE t.authorid = $uid ORDER BY t.dateline DESC LIMIT $start,$limit");
			return $query;
		}
		
		public function fetch_all_by_authorid_total($uid)
		{
			$query = DB::query("SELECT count(*) as num FROM ".DB::table('forum_thread')." WHERE authorid =".$uid);
			$count = DB::fetch($query);
			return $count['num'];
		}
		public function fetch_all_info($val)
		{
			$query = DB::query ( "SELECT subject,author,lastpost,views,replies FROM ".DB::table('forum_thread')." WHERE tid= ".$val.' limit 0,1');
			$rows = DB::fetch($query);
			return $rows;
		}
		public function forum_display()
		{
			$Tidquery =DB::query("SELECT t.tid FROM ".DB::table('forum_thread')." t,".DB::table('forum_forum')." f WHERE t.fid=f.fid AND f.status>0");
			while($dataForum = DB::fetch($Tidquery))
			{
				$tids[]=$dataForum['tid'];
			}
			$tids = implode(',', $tids);
			$tids =empty($tids)?0:$tids;
			return $tids;
		}
		public function fetch_all_hot($stamp,$start_limit,$limit,$fid,$tids)
		{
			if(empty($fid))
			{
				$query = DB::query( "SELECT * FROM ".DB::table('forum_thread')." t WHERE t.displayorder >= 0  AND (t.icon =10 or stamp =1) AND t.fid in(".$tids.")  ORDER BY t.tid desc limit " .$start_limit .",". $limit);
			}
			else {
				$query = DB::query( "SELECT * FROM  ".DB::table('forum_thread')." t  WHERE ".$stamp." and t.fid=".$fid." AND t.displayorder >= 0 AND (t.icon =10 or stamp =1) AND t.fid in(".$tids.") ORDER BY t.tid desc limit " .$start_limit .",". $limit);
			}
			while ($post = DB::fetch($query)) {
				$data[$post['tid']] = $post;
			
			}
			return $data;
		}
		public function fetch_all_search($conditions, $tableid = 0, $start = 0, $limit = 0, $order = '', $sort = 'DESC', $forceindex='') {
			$ordersql = '';
			if(!empty($order)) {
				$ordersql =  " AND displayorder >= 0 ORDER BY $order $sort ";
			}
			$data = array();
			$tlkey = !empty($conditions['inforum']) && !is_array($conditions['inforum']) ? $conditions['inforum'] : '';
			$firstpage = false;
			$defult = count($conditions) < 5 ? true : false;
			if(count($conditions) < 5) {
				foreach(array_keys($conditions) as $key) {
					if(!in_array($key, array('inforum', 'sticky', 'displayorder', 'intids'))) {
						$defult = false;
						break;
					}
				}
			}
			if($defult && $conditions['sticky'] == 4 && $start == 0 && $limit && strtolower(preg_replace("/\s?/ies", '', $order)) == 'displayorderdesc,lastpostdesc' && empty($sort)) {
				foreach($conditions['displayorder'] as $id) {
					if($id < 2) {
						$firstpage = true;
						if($id < 0) {
							$firstpage = false;
							break;
						}
					}
				}
				if($firstpage && !empty($tlkey) && ($ttl = getglobal('setting/memory/forum_thread_forumdisplay')) !== null && ($data = $this->fetch_cache($tlkey, 'forumdisplay_')) !== false) {
					$delusers = $this->fetch_cache('deleteuids', '');
					if(!empty($delusers)) {
						foreach($data as $tid => $value) {
							if(isset($delusers[$value['authorid']])) {
								$data = array();
							}
						}
					}
					if($data) {
						return $data;
					}
				}
			}
			$query = DB::query("SELECT * FROM ".DB::table('forum_thread')." where subject like '%".$conditions['keywords']."%' $ordersql limit $start, $limit");
			while($rows = DB::fetch($query))
			{
				$data[]=$rows;
			}
			if($firstpage && !empty($tlkey) && ($ttl = getglobal('setting/memory/forum_thread_forumdisplay')) !== null) {
				$this->store_cache($tlkey, $data, $ttl, 'forumdisplay_');
			}
			return $data;
		}
		public function count_hot_search($stamp,$displayorder,$fid,$tids)
		{
			if(!empty($displayorder))
			{
				$displayorder =implode($displayorder, ',');
				$displayorder =" and displayorder in (". $displayorder .")";
			}
			else
			{
				$displayorder ='';
			}
			if(empty($stamp))
			{
				$query = DB::query( "SELECT count(*) as num FROM ".DB::table('forum_thread')." t WHERE t.displayorder >= 0 and  t.fid=".$fid." AND t.fid in(".$tids.")  ORDER BY t.tid desc ");
			}
			else {
				$query = DB::query( "SELECT count(*) as num FROM  ".DB::table('forum_thread')." t  WHERE t.fid=".$fid." ".$stamp."   AND t.displayorder >= 0  AND t.fid in(".$tids.") ORDER BY t.tid desc  ");
			}
			$post = DB::fetch($query);
		
			return $post['num'];
		}
		public function fetch_all_threadimage($group)
		{
			$query = DB::query ( "SELECT B.tid,B.attachment,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $group ['tid']);
			$post = DB::fetch($query);
		
			return $post;
		}
		public function fetch($tid){
			$query = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid = ".$tid);
			return $query;
		}
		public function fetch_all_by_tid($tid)
		{
			$query = DB::query("SELECT * FROM ".DB::table('forum_thread')." WHERE tid IN(".$tid.")");
			while($value = DB::fetch($query)) {
				$data[$value['tid']] = $value;
			}
			return $data;
		}
		public function increase($tid){
			return	$query =DB::query("UPDATE ".DB::table('forum_thread')." SET favtimes = favtimes + 1 WHERE tid IN (".$tid.")");
		}
	}
?>