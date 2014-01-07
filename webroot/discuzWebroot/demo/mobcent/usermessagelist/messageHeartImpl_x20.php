<?php
require_once './abstarctMessageHeart.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../../uc_client/client.php';
require_once '../model/table/x20/mobcentDatabase.php';

class messageHeartImpl_x20 extends abstarctMessageHeart {
	public function getMessageHeartObj() {
		$uid = $_G ['userId'];  
		$uids =explode(',',$uid);
		foreach($uids as $key=>$uid)
		{
			if ((isset($_GET ['userId']) && !empty($_GET ['userId'])) || (isset($_GET ['accessSecret']) && !empty($_GET ['accessSecret'])) ) {
				if (count ( $followes )) {
					$followe = 1;
				}
				$blackfriend = DB::fetch_first ( 'SELECT uid FROM '.DB::table('home_blacklist').' WHERE buid='.$uid.' and uid='.$_GET ['accessSecret'].' limit 1' );
				if ($blackfriend ['uid'] == $_GET ['accessSecret']) {
					$is_black = 1;
				}
			
				$follow_num = DB::fetch_first ( "SELECT count(*) as count FROM ".DB::table('home_friend')." WHERE uid=".$uid);
				$status = DB::fetch_first ( "SELECT uid,invisible as status FROM ".DB::table('common_session')." WHERE uid=".$uid );
				if ($status ['uid'] == '')
					$status ['status'] = 1;
			
				$space = getuserbyuid ( $uid, 1 );  
				if ($space ['uid'] == '') {
					echo '{"rs":0,"errcode":"01000000"}';
					exit ();
				}
				$query = DB::query("SELECT * FROM ".DB::table('common_member_profile')." WHERE uid=".$space ['uid']);
				while($_G[$var] = DB::fetch($query))
				{
					$space_pro [$_G[$var] ['uid']] = array_merge($space, $_G[$var]);
				}
			
				space_merge ( $space, 'count' );
				$space = array_merge ( $space, $space_pro [$uid] );
				$data [$uid]['is_black'] = ( int ) $is_black;  
				$data [$uid]['is_follow'] = $follow_num ['count'] > 0?( int ) 1:( int ) 0;  
				$data [$uid]['icon_url'] = DISCUZSERVERURL;
				$data [$uid]['icon'] = userIconImg ( $uid ); 
				$data [$uid]['level_url'] = '';
				$data [$uid]['name'] = $space ['username'];
				$data [$uid]['email'] = $space ['email'];
				$data [$uid]['status'] = ( int ) 1 - $status ['status'];
				$data [$uid]['gender'] = ( int ) $space ['gender'];
				$data [$uid]['email'] = $space ['email'];
				$data [$uid]['credits'] = $space ['credits'];  
				$data [$uid]['gold_num'] = ( int ) $space ['extcredits2'];  
				$data [$uid]['topic_num'] = ( int ) $space ['threads'];
				$data [$uid]['reply_posts_num'] = ( int ) ($space ['posts'] - $space ['threads']);
				$data [$uid]['essence_num'] = ( int ) $space ['digestposts'];
				$data [$uid]['follow_num'] = $follow_num ['count']; 
				$data [$uid]['friend_num'] = $follow_num ['count']; 
				$data [$uid]['relational_num'] = 4;
				$groups = DB::fetch_first('select * from '.DB::table('common_usergroup').'  where groupid='.$space['groupid'].' limit 1');
				for($i=0;$i<=$groups['stars'];$i+=3){
					$level=$level * 3;
				}
				$data [$uid]['level'] = (int)$groups['stars'] * $level;  
				$data_pm['list'] =$data;
			}else
			{
				echo '{"rs":0,"errcode":"01000000"}';
			}
		}
		$data_pm['rs'] =1;
		
		return $data_pm;
		exit ();
		}

}

?>