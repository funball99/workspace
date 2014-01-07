<?php
class table_home_notification
{
	private static $tablename = 'home_notification';
	private static $_pk = 'pid';
	private static $_table='home_notification';
	
	public function fetch_by_fromid_uid($id, $idtype, $uid) {
		return DB::fetch_first("SELECT * FROM ".DB::table('home_notification')." WHERE from_id=".$id." AND from_idtype='".$idtype."' AND uid=".$uid);
	}
	public function fetch_all_by_authorid_fromid($authorid, $fromid, $type) {
		return DB::fetch_all("SELECT * FROM ".DB::table('home_notification')." WHERE authorid=".$authorid." AND from_id=".$fromid." AND type='".$type."'");
	}
	public static function fetch_all_by_uid($uid, $new, $type, $start, $perpage)
	{
		$new = intval($new);
		$type = $type ? ' AND ('.$type.')': '';
		$new = ' AND '.DB::field('new', $new);
		return DB::fetch_all("SELECT * FROM %t WHERE uid=%d %i %i ORDER BY new DESC, dateline DESC %i", array(self::$tablename, $uid, $type, $new, DB::limit($start, $perpage)));
	}
	public static function count_by_uid($uid, $new, $type,$tids)
	{
		$new = intval($new);
		
		$new = ' AND new ='.$new;
		$str = explode('=',$type);
		$arr =array(" 'post'");
		if($str[1] ===$arr[0])
		{
			$type = $type ? ' AND ('.$type.')': '';
			return DB::fetch_first("SELECT COUNT(DISTINCT a.id) AS num FROM ".DB::table('home_notification')." as a,".DB::table('forum_post')." as b WHERE  a.type='post' AND a.from_id=b.tid AND  a.uid = ".$uid.$new." AND b.fid IN(".$tids.")");
		}else{
			$type = $type ? ' '.$type.'': '';
			return DB::fetch_first("SELECT COUNT(DISTINCT a.id) AS num FROM ".DB::table('home_notification')." as a,".DB::table('forum_post')." as b WHERE  a.type='at' AND a.from_id=b.tid AND  a.uid = ".$uid.$new." AND b.fid IN(".$tids.")");
		}
	}

	public static function  isread($type, $uid){
		$data = DB::update('home_notification', array('new'=>0), array('type'=>$type,'new'=>1));
		return $data;
	}
	public static function get_notificationCount($uid,$type,$tids){
	if($type =='post')
		{
			$Psql = DB::fetch_first("SELECT  count(*) as num FROM ".DB::table('home_notification')." as a,".DB::table('forum_post')." as b WHERE  a.type='post' AND a.from_id=b.pid AND  a.uid = ".$uid." AND b.fid IN(".$tids.")");
			$Tsql = DB::fetch_first("SELECT  count(*) as num FROM ".DB::table('home_notification')." as a,".DB::table('forum_post')." as b WHERE  a.type='post' AND a.from_id=b.tid AND  a.uid = ".$uid." AND b.fid IN(".$tids.")");
		}else{
			$Tsql= DB::fetch_first("SELECT  count(*) as num FROM ".DB::table('home_notification')." as a,".DB::table('forum_post')." as b WHERE  a.type='at' AND a.from_id=b.tid AND  a.uid = ".$uid." AND b.fid IN(".$tids.")");
			$Psql= DB::fetch_first("SELECT  count(*) as num FROM ".DB::table('home_notification')." as a,".DB::table('forum_post')." as b WHERE  a.type='at' AND a.from_id=b.pid AND  a.uid = ".$uid." AND b.fid IN(".$tids.")");
		}
	
		return $Psql['num']+$Tsql['num'];
	}
	public static function get_notification($uid,$type,$start,$perpage,$tids){
		if($type =='post')
		{
			$query = DB::query("SELECT id,from_id,author,new,authorid,note FROM ".DB::table('home_notification')." WHERE type='post' AND  uid = ".$uid." ORDER BY dateline DESC limit ".$start.','.$perpage);
			while($rows = DB::fetch($query))
			{
				$ArrId[] = $rows;
			}
			foreach($ArrId as $key=>$val)
			{
				$postTids[]=$val['from_id'];
				$postreplys[$val['from_id']]=$val;
			}
				
			$postTids = implode(',',$postTids);
			$postTids = empty($postTids)?0:$postTids;
			$postreplyQuery = DB::query("SELECT tid,pid,fid,subject,message,dateline FROM ".DB::table('forum_post')." WHERE  fid in(".$tids.") AND tid in (".$postTids.") or pid in(".$postTids.") ORDER BY dateline DESC limit ".$start.','.$perpage );
			while($postReplyrows = DB::fetch($postreplyQuery))
			{
				$postreply[] = $postReplyrows;
			}
			foreach($postreplys as $key =>$Pval){
				foreach($postreply as $rkey=>$rval)
				{
					if($key ==$rval['tid'] || $key ==$rval['pid']){
						if($key ==$rval['tid']){
							$postArrReplys[$rval['tid']][]=array_merge($postreplys[$key],$rval);
							continue;
						}else if($key ==$rval['pid']){
							$postArrReplys[$rval['pid']][]=array_merge($postreplys[$key],$rval);
						}
					}
				}
			}
		}else{
			$query = DB::query("SELECT id,from_id,author,new,authorid,note FROM ".DB::table('home_notification')." WHERE type='at' AND  uid = ".$uid." ORDER BY dateline DESC limit ".$start.','.$perpage);
			while($rows = DB::fetch($query))
			{
				$ArrId[] = $rows;
			}
			foreach($ArrId as $key=>$val)
			{
				$postTids[]=$val['from_id'];
				$postreplys[$val['from_id']]=$val;
			}
			
			$postTids = implode(',',$postTids);
			$postTids = empty($postTids)?0:$postTids;
			$postreplyQuery = DB::query("SELECT tid,pid,fid,subject,message,dateline FROM ".DB::table('forum_post')." WHERE  fid in(".$tids.") AND tid in (".$postTids.") or pid in(".$postTids.") ORDER BY dateline DESC limit ".$start.','.$perpage );
			while($postReplyrows = DB::fetch($postreplyQuery))
			{
				$postreply[] = $postReplyrows;
			}
			foreach($postreplys as $key =>$Pval){
				foreach($postreply as $rkey=>$rval)
				{
					if($key ==$rval['tid'] || $key ==$rval['pid']){
						if($key ==$rval['tid']){
							$postArrReplys[$rval['tid']][]=array_merge($postreplys[$key],$rval);
							continue;
						}else if($key ==$rval['pid']){
							$postArrReplys[$rval['pid']][]=array_merge($postreplys[$key],$rval);
						}
					}
				}
			}
		}
		foreach($postArrReplys as $Pkey=>$Pval)
		{
			$postArr[]=$Pval[0];
		}
		return $postArr;
	}
}