<?php
require_once './abstractModworkList.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../public/mobcentDatabase.php';
require_once '../../uc_client/client.php';
require_once '../model/table/x20/table_common_member.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../model/table/x20/mobcentDatabase.php';

class modworkListImpl_x20 extends abstractModworkList { 

	function getModworkListObj() {
		$info = new mobcentGetInfo();
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
		$group = $info->rank_check_allow($accessSecret,$accessToken,$qquser);
		if(!$group['allowvisit'])
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '01110001';
			return $data_post;
			exit();
		}
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$_G['uid'] = $uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		$page = empty ( $_GET ['page'] ) ? 0 : intval ( $_GET ['page'] );
		$perpage = empty ( $_GET ['pageSize'] ) ? 15 : intval ( $_GET ['pageSize'] );
		
		$uids =explode(',',$uid);
		foreach($uids as $key=>$uid)
		{
			if ((isset($_GET ['userId']) && !empty($_GET ['userId'])) || (isset($_GET ['accessSecret']) && !empty($_GET ['accessSecret'])) ) {
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
				
				
				$data ['msg_relation_id'] = '';  
				$data ['created_date'] = ''; 
				$data ['user_id'] = $uid;
				$data ['icon'] = userIconImg ( $uid ); 
				$data ['to_user_id'] = '';
				$data ['nick_name'] = $space ['username'];
				$data ['content'] = '';
				$data ['to_nick_name'] = '';
				$data ['uid'] = ( int )$uid;
				$data ['name'] = $space ['username'];
				
				$groups = DB::fetch_first('select * from '.DB::table('common_usergroup').'  where groupid='.$space['groupid'].' limit 1');
				for($i=0;$i<=$groups['stars'];$i+=3){
					$level=$level * 3;
				}
				$data ['level'] = (int)$groups['stars'] * $level; 
				$data_pm['list'][] =$data;
				$data_pm['rs'] =1;
				$N = ceil ( $count / $perpage );
				$page = empty ( $_GET ['page'] ) ? 0 : intval ( $_GET ['page'] );
				$data_pm ['icon_url'] = DISCUZSERVERURL;  
				$data_pm ['rs'] = (Int)1; 
				$data_pm ['page'] = (Int)$page;
				$data_pm ['has_next'] = ($page >= $N || $N == 1) ? 0 : 1;  
			}else
			{
				$data_pm['rs'] =0;
				$data_pm['errcode'] ="01000000";
			}
		}
		
		return $data_pm;
		}

}

?>