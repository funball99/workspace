<?php
class table_home_follow {
	private $_table = 'home_friend';
	function notification_add($touid, $uid,$username,$type, $note, $notevars = array(), $system = 0) {
		global $_G;
	
		$tospace = array('uid'=>$touid);
		space_merge($tospace, 'field_home');
		$filter = empty($tospace['privacy']['filter_note'])?array():array_keys($tospace['privacy']['filter_note']);
	
		if($filter && (in_array($type.'|0', $filter) || in_array($type.'|'.$_G['uid'], $filter))) {
			return false;
		}
	
		$notevars['actor'] = "<a href=\"home.php?mod=space&uid=$uid\">".$username."</a>";
		if(!is_numeric($type)) {
			$vars = explode(':', $note);
			if(count($vars) == 2) {
				$notestring = lang('plugin/'.$vars[0], $vars[1], $notevars);
			} else {
				$notestring = lang('notification', $note, $notevars);
			}
			$frommyapp = false;
		} else {
			$frommyapp = true;
			$notestring = $note;
		}
	
		$oldnote = array();
		if($notevars['from_id'] && $notevars['from_idtype']) {
			$oldnote = DB::fetch_first("SELECT * FROM ".DB::table('home_notification')."
					WHERE from_id='$notevars[from_id]' AND from_idtype='$notevars[from_idtype]' AND uid='$touid'");
		}
		if(empty($oldnote['from_num'])) $oldnote['from_num'] = 0;
		$notevars['from_num'] = $notevars['from_num'] ? $notevars['from_num'] : 1;
		$setarr = array(
				'uid' => $touid,
				'type' => $type,
				'new' => 1,
				'authorid' => $uid,
				'author' => $username,
				'note' => addslashes($notestring),
				'dateline' => time(),
				'from_id' => $notevars['from_id'],
				'from_idtype' => $notevars['from_idtype'],
				'from_num' => ($oldnote['from_num']+$notevars['from_num'])
		);
		if($system) {
			$setarr['authorid'] = 0;
			$setarr['author'] = '';
		}
	
		if($oldnote['id']) {
			DB::update('home_notification', $setarr, array('id'=>$oldnote['id']));
		} else {
			$oldnote['new'] = 0;
			DB::insert('home_notification', $setarr);
		}
	
		if(empty($oldnote['new'])) {
			DB::query("UPDATE ".DB::table('common_member')." SET newprompt=newprompt+1 WHERE uid='$touid'");
	
			require_once libfile('function/mail');
			$mail_subject = lang('notification', 'mail_to_user');
			sendmail_touser($touid, $mail_subject, $notestring, $frommyapp ? 'myapp' : $type);
		}
	
		if(!$system && $_G['uid'] && $touid != $_G['uid']) {
			DB::query("UPDATE ".DB::table('home_friend')." SET num=num+1 WHERE uid='$_G[uid]' AND fuid='$touid'");
		}
	}
	
	public function fetch_status_by_uid_followuid($uid, $followuid) {
		$query = DB::query ( 'SELECT * FROM '.DB::table($this->_table).' WHERE (uid=' . $uid . ' AND fuid=' . $followuid . ') OR (uid=' . $followuid . ' AND fuid=' . $uid );
		while ( $rows = DB::fetch ( $query ) ) {
			$arr [$rows ['uid']] = $rows;
		}
		return $arr;
	}
    public function fetch_all_following_by_uid($uid, $start = 0, $limit = 0) {
		$data = array();
		$wherearr = array();
		$force = !$start && !$limit? false : true;
		if((!$force && ($data = $this->fetch_cache($uid)) === false) || $force) {
			$wherearr[] = 'uid='.$uid;
			$wheresql = !empty($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
			$query = DB::query("SELECT * FROM ".DB::table($this->_table). $wheresql." ORDER BY dateline DESC LIMIT ". $start.",".$limit);
            $data = '';
            while ($followList = DB::fetch($query)){
            	$data[] = $followList;
            }
		}
		return $data;
	}
public function count_follow_user($uid, $type = 0, $dateline = 0) {
		$field = $type ? 'fuid' : 'uid';
		$count = DB::result_first("SELECT COUNT(*) as count FROM ".DB::table($this->_table). " WHERE $field = ".$uid);
		return $count;
	}
	public function fetch_by_uid_followuid($uid, $followuid) {
		return DB::fetch_first ( "SELECT * FROM " . DB::table($this->_table) . "  WHERE fuid=" . $uid . " AND uid=" . $followuid );
	}
	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		if ($data && is_array ( $data )) {
			return DB::insert ( $this->_table, $data, $return_insert_id, $replace, $silent );
		}
		return 0;
	}
	public function delete_by_uid_followuid($uid, $followuid) {
		return DB::delete ( $this->_table, array (
				'uid' => $uid,
				'fuid' => $followuid 
		) );
	}
public function fetch_all_by_followuid($uid, $start = 0, $limit = 0) {
		$data = array();
		$wherearr = array();
		$force = !$start && !$limit ? false : true;
		if((!$force && ($data = $this->fetch_cache($uid)) === false) || $force) {
			$wherearr[] = 'fuid='.$uid;
			$wheresql = !empty($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
			$query = DB::query("SELECT * FROM ".DB::table($this->_table)." $wheresql ORDER BY dateline DESC LIMIT ".$start .",". $limit);
			$data ='';
			while ($array = DB::fetch($query)){
				$data[] = $array;
			}
		}
		return $data;
	}
}
?>
