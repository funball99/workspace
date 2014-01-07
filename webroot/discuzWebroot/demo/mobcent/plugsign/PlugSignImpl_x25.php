<?php
require_once './abstractPlugSign.php';
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

class PlugSignImpl_x25 extends abstractPlugSign { 
	public function getPlugSignObj() { 
		function isdate($str,$format="Y-m-d"){
			$strArr = explode("-",$str);
			if(empty($strArr)){
				return false;
			}
			foreach($strArr as $val){
				if(strlen($val)<2){
					$val="0".$val;
				}
				$newArr[]=$val;
			}
			$str =implode("-",$newArr);
			$unixTime=strtotime($str);
			$checkDate= date($format,$unixTime);
			if($checkDate==$str){
				return true;
			}else{
				return false;
			}
		}
		
		$rPostion = $_GET['r'] ? $_GET['r']:0;  
		$longitude =$_GET['longitude'];	
		$latitude =	$_GET['latitude'];	
		$location	=	echo_urldecode($_GET['location']);
		$aid = $_REQUEST ['aid']; 
		$aid_Img=explode(',',$aid);
		$_G ['fid'] = $_GET ['boardId'];
		require_once '../public/mobcentDatabase.php';
		$info = new mobcentGetInfo ();
		$modnewposts = $info ->getBoard($_G ['fid']);
		$readperm = 0;
		$price = 0;
		$typeid = 0;
		$sortid = 0;
		$displayorder = $modnewposts['modnewposts'] > 0?-2:0;
		$digest = 0;
		$special = 0;
		$attachment = 0;
		$moderated = 0;
		$isgroup = 0;
		$replycredit = 0;
		$closed = 0;
		$publishdate = time ();
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
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$ruid = $_G ['uid'] =$arrAccess['user_id'];
		$space = $info->getUserInfo ( intval ( $ruid ) );
		if(empty($_G ['uid']))
		{
			return $info -> userAccessError();
			exit();
		}
		$author = $space ['username'];
		$_G ['username'] = $lastposter = $author;
		$_G = array_merge ( $_G, $space );
		 
	 	/*renxing sign...2013-07-03*/
		$var_query = DB::query("SELECT variable,value FROM ".DB::table('common_pluginvar')." order by displayorder asc");
		while ($var_result = DB::fetch($var_query)) {
			$var_arr[] = $var_result;
		}
		
		/*mei you an zhuang qian dao cha jian*/
		if(empty($var_arr)){
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000058';  
			return $data_post;
			exit();
		}
		
		foreach($var_arr as $arrval){
			$var[$arrval[variable]]=$arrval[value];
		} 
		$njlmain =str_replace(array("\r\n", "\n", "\r"), '/hhf/', $var['jlmain']);
		$extreward = explode("/hhf/", $njlmain);
		$extreward_num = count($extreward);
		$_GET[qdxq]='kx';
		
		/*shi fou kai qi qian dao cha jian*/
		if($var[ifopen]==0 && $_G ['uid']!=1){
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000050';
			return $data_post;
			exit();
		}
		
		/*shi fou kai qi qian dao shi jian duan xian zhi*/
		$htime = dgmdate(time(), 'H',$var['tos']); /*dang qian xiao shi*/
		if($var['timeopen']) {
			if ($htime < $var['stime'] || $htime > $var['ftime']) {
				$data_post['rs'] = 0;
				$data_post['errcode'] = '04000051';
				return $data_post;
				exit();
			} 
		}
		
		/*qian dao yong hu zong fa tie shu xian zhi*/
		$post = DB::fetch_first("SELECT posts FROM ".DB::table('common_member_count')." WHERE uid='$_G[uid]'");
		if($var['mintdpost'] > $post['posts']){
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000052';
			return $data_post;
			exit();
		}
		
		/*jin zhi qian dao de hui yuan(hei ming dan)*/
		$read_ban = explode(",",$var['ban']);
		if(in_array($_G['uid'],$read_ban)){
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000053';
			return $data_post;
			exit();
		}
		
		/*jin ri yi jing qiao dan*/
		$qiandaodb = DB::fetch_first("SELECT * FROM ".DB::table('dsu_paulsign')." WHERE uid='$_G[uid]'");
		$tdtime = gmmktime(0,0,0,dgmdate(time(), 'n',$var['tos']),dgmdate(time(), 'j',$var['tos']),dgmdate(time(), 'Y',$var['tos'])) - $var['tos']*3600;
		if($qiandaodb['time']>$tdtime){
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000054';
			return $data_post;
			exit();
		}
		
		/*yun xu shi yong de yong hu zu*/
		$groups = unserialize($var['groups']);
		$gps = unserialize($var['GROUPS']);
		if(isset($var['groups']) && is_array($groups)){
			if(!in_array($_G['groupid'], $groups)){
				$data_post['rs'] = 0;
				$data_post['errcode'] = '04000055';
				return $data_post;
				exit();
			}
		}elseif(isset($var['GROUPS']) && is_array($gps)){
			if(!in_array($_G['groupid'], $gps)){
				$data_post['rs'] = 0;
				$data_post['errcode'] = '04000055';
				return $data_post;
				exit();
			}
		}
		
		
		/*mei ri zui xiang shuo*/
		$todaysay="";
		
		/*qiao dao jin cheng suo*/
		if($var['lockopen']){
			while(discuz_process::islocked('dsu_paulsign', 5)){
				usleep(100000);
			}
		}
		 
		$jlxgroups = unserialize($var['jlxgroups']);
		$credit = mt_rand($var['mincredit'],$var['maxcredit']);
		if(in_array($_G['groupid'], $jlxgroups) && $var['jlx'] !== '0') {
			$credit = $credit * $var['jlx'];
		}
		if(($tdtime - $qiandaodb['time']) < 86400 && $var['lastedop'] && $qiandaodb['lasted'] !== '0'){
			$randlastednum = mt_rand($var['lastednuml'],$var['lastednumh']);
			$randlastednum = sprintf("%03d", $randlastednum);
			$randlastednum = '0.'.$randlastednum;
			$randlastednum = $randlastednum * $qiandaodb['lasted'];
			$credit = round($credit*(1+$randlastednum));
		}
		$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulsign')." WHERE time >= {$tdtime} ");
		if(!$qiandaodb['uid']) {
			DB::query("INSERT INTO ".DB::table('dsu_paulsign')." (uid,time) VALUES ('$_G[uid]','".time()."')");
		}
		if(($tdtime - $qiandaodb['time']) < 86400 && $var['lastedop']){
			DB::query("UPDATE ".DB::table('dsu_paulsign')." SET days=days+1,mdays=mdays+1,time='".time()."',qdxq='$_GET[qdxq]',todaysay='$todaysay',reward=reward+{$credit},lastreward='$credit',lasted=lasted+1 WHERE uid='$_G[uid]'");
		} else {
			DB::query("UPDATE ".DB::table('dsu_paulsign')." SET days=days+1,mdays=mdays+1,time='".time()."',qdxq='$_GET[qdxq]',todaysay='$todaysay',reward=reward+{$credit},lastreward='$credit',lasted='1' WHERE uid='$_G[uid]'");
		}
		updatemembercount($_G['uid'], array($var['nrcredit'] => $credit));
		$another_vip = '';
	
		$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulsign')." WHERE time >= {$tdtime} ");
		if($num <= ($extreward_num - 1) ) {
			list($exacr,$exacz) = explode("|", $extreward[$num]);
			$psc = $num+1;
			if($exacr && $exacz) updatemembercount($_G['uid'], array($exacr => $exacz));
		}
		
		/*qiao dao de zi dong hui fu lei xing   ....qdtype */
		/*end qdtype*/
		if(memory('check')) memory('set', 'dsu_pualsign_'.$_G['uid'], $_G['timestamp'], 86400);
		$stats = DB::fetch_first("SELECT * FROM ".DB::table('dsu_paulsignset')." WHERE id='1'");
		if($num ==0) {
			if($stats['todayq'] > $stats['highestq']) DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET highestq='$stats[todayq]' WHERE id='1'");
			DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET yesterdayq='$stats[todayq]',todayq=1 WHERE id='1'");
			DB::query("UPDATE ".DB::table('dsu_paulsignemot')." SET count=0");
		} else {
			DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET todayq=todayq+1 WHERE id='1'");
		}
		DB::query("UPDATE ".DB::table('dsu_paulsignemot')." SET count=count+1 WHERE qdxq='$_GET[qdxq]'");
		if($var['lockopen']) discuz_process::unlock('dsu_paulsign');
		
		$data_post['rs'] = 1;
		return $data_post;	
	
	}
	
}

?>