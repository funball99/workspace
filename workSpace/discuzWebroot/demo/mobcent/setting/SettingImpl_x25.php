<?php
require_once './abstractSetting.php';
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

class SettingImpl_x25 extends abstractSetting { 
	public function getPlugSignObj() { 
		$xm=new topic();
		$s=file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
		$result =$xm->xml_to_array($s); 
		//print_r($result);exit;
		
		/*---weiboshow---*/
		$weiboshow=$result['weiboshow'][0][0];
		if($weiboshow==1){
			$data['weiboshow'] = 0;
		}else{
			$data['weiboshow'] = 0;
		}
		/*---end weiboshow---*/
		
		/*---QQshow---*/
		$qq_query = DB::query("SELECT * FROM ".DB::table('common_plugin'));
		while ($qq_list = DB::fetch($qq_query)) {
			$qq_arr[] = $qq_list;
		}
		$ts="";
		foreach($qq_arr as $qa){
			$rst=$qa[identifier].'@'.$qa[available].'@';
			$ts.=$rst;
		}
		$m1=strpos($ts, "qqconnect@");
		$isqq=substr($ts,($m1+10),1);
		$qqshow=$result['qqshow'][0][0];
		if($isqq==1 && $qqshow==1){
			$data['qqshow'] = 1;
		}else{
			$data['qqshow'] = 0;
		}
		/*---end QQshow---*/
		
		
		/*---plugsign---*/
		$var_query = DB::query("SELECT * FROM ".DB::table('common_pluginvar'));
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
			$data['plugcheck'] = 1;
		}else{
			$data['plugcheck'] = 0;
		}
		/*--end plugsign--*/
		$data['serverTime'] = time().'000';
		$data['rs'] = 1;
		$data_post=$data;
		return $data_post;
	
	}
	
}

?>