<?php
require_once './abstractPostList.php';
define('IN_MOBCENT',1);
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../model/table/x20/mobcentDatabase.php';


class PostListImpl_x20 extends abstractPostList {
	public function getPostListObj() {
		
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
		$userId = $arrAccess['user_id'];
		$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
		if(empty($accessSecret) || empty($accessToken))
		{
			$query = $info-> sel_QQuser($qquser);
			while($arr = DB::fetch($query))
			{
				$group =$arr;
			}
		
		}else if(empty($userId))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}else {
			$group = $info-> sel_group_by_uid($userId);
			
		}
		if(!$_G['forum']['viewperm'] && !$group['readaccess'])
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '01110001';
			return $data_post;
			exit();
		}
		$space = $info->getUserInfo(intval($userId));
		$_G=array_merge($_G,$space);
		$_G['fid'] = intval($_GET['boardId']);
		$forum = $info->getForumSub($_G['fid']);
		$_G['forum']=array_merge($_G['forum'],$forum);
		
		/*renxing fenlei*/
		$fenlei_id = intval($_GET['classificationTop_id']);
		$classified_query = DB::query("SELECT a.optionid,a.classid as classifiedTopId,a.title as classifiedTitle,a.identifier as classifiedName,a.type as classifiedType,a.rules as classifiedRules,b.* FROM ".DB::table('forum_typeoption')." as a left join ".DB::table('forum_typevar')." as b  on a.optionid=b.optionid WHERE b.sortid=".$fenlei_id." order by b.displayorder asc");
		
		while ($classified_result = DB::fetch($classified_query)) {
			$classified_list[] = $classified_result;
		} 
		
		/*-----------check bi tian----------------*/
		$mytypevar_query = DB::query("SELECT * FROM ".DB::table('forum_typevar')." WHERE sortid=".$fenlei_id." order by displayorder asc");
		while ($mytypevar_result = DB::fetch($mytypevar_query)) {
			$mytypevar[] = $mytypevar_result;
		}
		foreach($mytypevar as $tpvs){
			$typevar[]=$tpvs;
		}
		foreach($classified_list as $dd=>$ff){
			$classified_test[]=array_merge($ff,$typevar[$dd]);
		}
		foreach($typevar as $trs){
			unset($trs[sortid]);
			unset($trs[available]);
			unset($trs[search]);
			$rx_var[]=$trs;
		}
		for($sx=0;$sx<count($classified_test);$sx++){
			if(intval($classified_test[$sx][available])==0){
				unset($classified_test[$sx]);
			}
		}
		
		$classified_arr=array();
		foreach($classified_test as $cs_test){
			unset($cs_test[sortid]);
			unset($cs_test[optionid]);
			unset($cs_test[available]);
			unset($cs_test[search]);
			unset($cs_test[displayorder]);
			unset($cs_test[subjectshow]);
			$classified_arr[]=$cs_test;
		} 
		/*-----------end check----------------*/
		
		$renxing=array();
		for($i=0;$i<count($classified_arr);$i++){			
			$tps=$classified_arr[$i][classifiedType];
			
			$classified_arr[$i][classifiedRules]=unserialize($classified_arr[$i][classifiedRules]);
			if(isset($classified_arr[$i][classifiedRules][maxlength]) && is_numeric(($classified_arr[$i][classifiedRules][maxlength]))){
				$classified_arr[$i][classifiedRules][maxlength]=intval((int)$classified_arr[$i][classifiedRules][maxlength]/3);
			}
			if($tps=="calendar"){
				$classified_arr[$i][classifiedRules][defaultvalue]=date("Y-m-d",time());
				$classified_arr[$i][classifiedRules][isdate]=1;
			}
			if($tps=="number" || $tps=="range"){
				$classified_arr[$i][classifiedRules][isnumber]=1;
			}
			
			if($tps=="calendar" || $tps=="email" || $tps=="url" || $tps=="number" || $tps=="range"){
				$classified_arr[$i][classifiedType]="text";
			}
			switch($classified_arr[$i][classifiedType]){
				case "text":
					$classified_arr[$i][classifiedType]=1;
					break;
				case "radio":
					$classified_arr[$i][classifiedType]=2;
					break;
				case "checkbox":
					$classified_arr[$i][classifiedType]=3;
					break;
				case "select":
					$classified_arr[$i][classifiedType]=4;
					break;
				case "textarea":
					$classified_arr[$i][classifiedType]=5;
					break;
				case "image":
					$classified_arr[$i][classifiedType]=6;
					break;
				default:
					$classified_arr[$i][classifiedType]=0;
					break;
			}
			
			$choice_arr=explode("\r\n",$classified_arr[$i][classifiedRules][choices]);			
     		if($classified_arr[$i][classifiedRules][choices]!=""){
			foreach($choice_arr as $charr){
				$choice_val=explode("=",$charr);
				if($choice_val[0]==intval($choice_val[0])){
					$aaa['name']=$choice_val[1];
					$aaa['value']=$choice_val[0];
					$cd[]=$aaa;				 
					foreach($renxing as $rx){
						$msd=count($rx);
					}
				}
			}			
			$classified_arr[$i][classifiedRules][choices]=array_slice($cd,$msd); 
			$renxing[]=$cd;
		}
			
		if($classified_arr[$i][classifiedRules]==false){
			$classified_arr[$i][classifiedRules]=array();
		}
	} 
	 
		/*end renxing fenlei*/
		$data_post["classificationTopId"]=$fenlei_id;
		$data_post ["classified"] = $classified_arr;
		$data_post ["rs"] = 1;
		
		return $data_post;
	}

}

?>