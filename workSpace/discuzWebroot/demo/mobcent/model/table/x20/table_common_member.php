<?php 
	class table_common_member
	{
		public function userAccessError()
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = 50000000;
			return $data_post;
		}
		public function update_accessTopkent($accessToken,$accessSecret,$uid,$time)
		{
			$query = DB::query("UPDATE ".DB::table('home_access')." SET user_access_secret = '".$accessSecret."',create_time = '".$time."' WHERE user_id ='".$uid."' AND  user_access_token ='".$accessToken."'");
			return $query;
		}
		public function sel_accessTopkentByUid($uid)
		{
			$query = DB::fetch_first('SELECT user_access_token, user_access_secret FROM '.DB::table('home_access').' WHERE user_id ='.$uid);
			return $query;
		}
		public function sel_accessTopkent($accessSecret,$accessToken)
		{
			$query = DB::fetch_first("SELECT * FROM ".DB::table('home_access')." WHERE user_access_token ='".$accessToken."' AND user_access_secret = '".$accessSecret."'");
			return $query;
		}
		
		public function inser_accessTopkent($accessToken,$accessSecret,$uid,$time)
		{
			$query = DB::query("INSERT INTO ".DB::table('home_access')." VALUES(user_access_id,'".$accessToken."','".$accessSecret."',".$uid.",'".$time."')");
			return $query;
		}
		public static function getUserStatus($uids){
			$uid = implode(",",$uids);
			$limit = count($uids);
			return DB::query('SELECT uid,username,status FROM '.DB::table(common_member).' WHERE uid in('.$uid.') ORDER BY uid desc LIMIT '.$limit);
			
		}
		public function updateUserStatus($uid,$status){
			$query = DB::fetch_first("UPDATE ".DB::table('common_member') ." SET status =".$status." WHERE uid = " .$uid);
			return $query;
		}
		public static function getUsergroup($uid){
			return DB::fetch_first('SELECT uid,username,groupid FROM '.DB::table(common_member).' WHERE uid = '.$uid);
				
		}
		public static function userAccess($groupid){
			$userAllow = DB::fetch_first("SELECT * FROM ".DB::table('common_usergroup')." WHERE groupid = ".$groupid);
			return $userAllow;
		}
		public static function getUserId($username){
			return DB::fetch_first("SELECT uid FROM ".DB::table(common_member)." WHERE username = '".$username."'");
		
		}
	}
?>