<?php
require_once './abstractPlugCheck.php';
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/class/table/table_forum_thread.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/constants.php';
require_once '../model/table_forum_thread.php';
require_once '../helper/helper_notification.php';
require_once '../model/table_surround_user.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'class/credit' );
require_once libfile ( 'function/post' );
require_once libfile ( 'function/forum' );
require_once '../model/table/x25/table_common_member.php';
require_once '../model/table/x25/topic.php';

class PlugCheckImpl_x25 extends abstractPlugCheck { 
	public function getPlugSignObj() { 
		$var_query = DB::query("SELECT variable FROM ".DB::table('common_pluginvar'));
		while ($var_result = DB::fetch($var_query)) {
			$var_arr[] = $var_result;
		}
		$qiandao=array('lastedop','autosign_ug','sync_follow','lockopen','sayclose','lastednuml','lastednumh','tos','sync_sign','mcacheopen','sync_say','sidebarmode','wap_sign','todaysayxt','ksopen','fastreplytext','qdtypeid','tzopen','maxcredit','jlx','jlmain','plgroups','qdtype','stime','ftopen','ifopen');
		foreach($var_arr as $varr){
			if(in_array($varr[variable], $qiandao)){
				$res=true;
			}
		}
		if($res==true){
			$data_post['rs'] = 1;
		}else{
			$data_post['rs'] = 0;
		}
		return $data_post;
	
	}
	
}

?>