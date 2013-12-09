<?php
define ( 'IN_MOBCENT', 1 );
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../config/config_ucenter.php';
require_once '../../source/function/function_forumlist.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/img_do.php';
require_once '../tool/Thumbnail.php';
require_once '../../source/function/function_forum.php';
require_once '../public/yz.php';
require_once '../tool/constants.php'; 
require_once './abstractSetting.php';
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/table_common_member.php';
require_once '../model/table/x20/table_forum_announcement.php';
require_once '../model/table/x20/mobcentDatabase.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();

class SettingImpl_x20 extends abstractSetting {  
	public function getPlugSignObj() {
		$xm=new topic();
		$s=file_exists('../manage/App.xml')?join("",file('../manage/App.xml')):array();
		$result =$xm->xml_to_array($s);  

		/*---weiboshow---*/
		$weiboshow=$result['weiboshow'][0][0][0];
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
		foreach($qq_arr as $qa){
			if($qa[identifier]="qqconnect"){
				$isqq=$qa[available];break;
			}
		}
		$qqshow=$result['qqshow'][0][0];
		if($isqq==1 && $qqshow==1){
			$data['qqshow'] = 0;
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
		
		$data['rs'] = 1;
		$data_post=$data;
		return $data_post;
		
	}

}

?>