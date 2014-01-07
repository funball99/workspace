<?php 
ob_end_clean ();
@session_start();
define('APPTYPEID', 2);
$session_file='session.txt';
$session_time=600; /*10 minutes*/
/*
if(!file_exists($session_file)){
	fopen("$session_file", "w+");
	file_put_contents($session_file, time());
}
$lasttime = file_get_contents($session_file);
if(time()-$lasttime>$session_time){
	unlink($session_file);
	unset($_SESSION["admin"]);
	$_SESSION["admin"] = false;
}else{
	file_put_contents($session_file, time());
}*/

if(isset($_SESSION['renxing']) && $_SESSION['renxing']==true){
define('IN_MOBCENT',1);

require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_add_portal_module.php';
require_once '../Config/public.php';
require_once '../model/table_common_member_profile.php';
require_once '../public/mobcentDatabase.php';
require_once '../install/checkModule.php';
require_once libfile('function/forumlist');
class manageImpl_x25{ 
	public function getManageObj() {
		C::app ()->init();
		loadforum();
		set_rssauth();
		runhooks();
		$info = new mobcentGetInfo();
		 $space = $info->getUserInfo(intval($_GET['accessSecret']));
		 $_G=array_merge($_G,$space);
		
		
	 
		$gid = intval(getgpc('gid'));
		$catlist = $forumlist = $sublist = $forumname = $collapse = $favforumlist = array();
		if(!$gid) {
			$forums = C::t('forum_forum')->fetch_all_by_status(1);
			
			$fids = array();
			foreach($forums as $forum) {
				$fids[$forum['fid']] = $forum['fid'];
			}
		
			$forum_access = array();
			if(!empty($_G['member']['accessmasks'])) {
				$forum_access = C::t('forum_access')->fetch_all_by_fid_uid($fids, $_G['uid']);
			}
		
			$forum_fields = C::t('forum_forumfield')->fetch_all($fids);
			foreach($forums as $forum) {
				if($forum_fields[$forum['fid']]['fid']) {
					$forum = array_merge($forum, $forum_fields[$forum['fid']]);
				}
				if($forum_access['fid']) {
					$forum = array_merge($forum, $forum_access[$forum['fid']]);
				}
				$forumname[$forum['fid']] = strip_tags($forum['name']);
				$forum['extra'] = empty($forum['extra']) ? array() : dunserialize($forum['extra']);
				if(!is_array($forum['extra'])) {
					$forum['extra'] = array();
				}
		
				if($forum['type'] != 'group') {
		
					$threads += $forum['threads'];
					$posts += $forum['posts'];
					$todayposts += $forum['todayposts'];
		
					if($forum['type'] == 'forum' && isset($catlist[$forum['fup']])) {
						if(forum_2($forum)) {
							$catlist[$forum['fup']]['forums'][] = $forum['fid'];
							$forum['orderid'] = $catlist[$forum['fup']]['forumscount']++;
							$forum['subforums'] = '';
							$forumlist[$forum['fid']] = $forum;
						}
		
					} elseif(isset($forumlist[$forum['fup']])) {
		
						$forumlist[$forum['fup']]['threads'] += $forum['threads'];
						$forumlist[$forum['fup']]['posts'] += $forum['posts'];
						$forumlist[$forum['fup']]['todayposts'] += $forum['todayposts'];
						if($_G['setting']['subforumsindex'] && $forumlist[$forum['fup']]['permission'] == 2 && !($forumlist[$forum['fup']]['simple'] & 16) || ($forumlist[$forum['fup']]['simple'] & 8)) {
							$forumurl = !empty($forum['domain']) && !empty($_G['setting']['domain']['root']['forum']) ? 'http://'.$forum['domain'].'.'.$_G['setting']['domain']['root']['forum'] : 'forum.php?mod=forumdisplay&fid='.$forum['fid'];
							$forumlist[$forum['fup']]['subforums'] .= (empty($forumlist[$forum['fup']]['subforums']) ? '' : ', ').'<a href="'.$forumurl.'" '.(!empty($forum['extra']['namecolor']) ? ' style="color: ' . $forum['extra']['namecolor'].';"' : '') . '>'.$forum['name'].'</a>';
						}
					}
		
				} else {
		
					if($forum['moderators']) {
					 	$forum['moderators'] = moddisplay($forum['moderators'], 'flat');
					}
					$forum['forumscount'] 	= 0;
					$catlist[$forum['fid']] = $forum;
		
				}
			}
		}else {
			$gquery = C::t('forum_forum')->fetch_all_info_by_fids($gid);
			$query = C::t('forum_forum')->fetch_all_info_by_fids(0, 1, 0, $gid, 1, 0, 0, 'forum');
			if(!empty($_G['member']['accessmasks'])) {
				$fids = array_keys($query);
				$accesslist = C::t('forum_access')->fetch_all_by_fid_uid($fids, $_G['uid']);
				foreach($query as $key => $val) {
					$query[$key]['allowview'] = $accesslist[$key];
				}
			}
			$query = array_merge($gquery, $query);
			$fids = array();
			foreach($query as $forum) {
				$forum['extra'] = dunserialize($forum['extra']);
				if(!is_array($forum['extra'])) {
					$forum['extra'] = array();
				}
				if($forum['type'] != 'group') {
					$threads += $forum['threads'];
					$posts += $forum['posts'];
					$todayposts += $forum['todayposts'];
					if(forum($forum)) {
						$forum['orderid'] = $catlist[$forum['fup']]['forumscount'] ++;
						$forum['subforums'] = '';
						$forumlist[$forum['fid']] = $forum;
						$catlist[$forum['fup']]['forums'][] = $forum['fid'];
						$fids[] = $forum['fid'];
					}
				} else {
					$forum['collapseimg'] = 'collapsed_no.gif';
					$collapse['category_'.$forum['fid']] = '';
			
					if($forum['moderators']) {
						$forum['moderators'] = moddisplay($forum['moderators'], 'flat');
					}
					$catlist[$forum['fid']] = $forum;
			
					$navigation = '<em>&rsaquo;</em> '.$forum['name'];
					$navtitle_g = strip_tags($forum['name']);
				}
			}
			unset($forum_access, $forum_fields);
			if($catlist) {
				foreach($catlist as $key => $var) {
					$catlist[$key]['forumcolumns'] = $var['catforumcolumns'];
					if($var['forumscount'] && $var['catforumcolumns']) {
						$catlist[$key]['forumcolwidth'] = (floor(100 / $var['catforumcolumns']) - 0.1).'%';
						$catlist[$key]['endrows'] = '';
						if($colspan = $var['forumscount'] % $var['catforumcolumns']) {
							while(($var['catforumcolumns'] - $colspan) > 0) {
								$catlist[$key]['endrows'] .= '<td>&nbsp;</td>';
								$colspan ++;
							}
							$catlist[$key]['endrows'] .= '</tr>';
						}
					}
				}
				unset($catid, $category);
			}
			$query = C::t('forum_forum')->fetch_all_subforum_by_fup($fids);
			foreach($query as $forum) {
				if($_G['setting']['subforumsindex'] && $forumlist[$forum['fup']]['permission'] == 2) {
					$forumurl = !empty($forum['domain']) && !empty($_G['setting']['domain']['root']['forum']) ? 'http://'.$forum['domain'].'.'.$_G['setting']['domain']['root']['forum'] : 'forum.php?mod=forumdisplay&fid='.$forum['fid'];
					$forumlist[$forum['fup']]['subforums'] .= '<a href="'.$forumurl.'"><u>'.$forum['name'].'</u></a>&nbsp;&nbsp;';
				}
				$forumlist[$forum['fup']]['threads'] 	+= $forum['threads'];
				$forumlist[$forum['fup']]['posts'] 	+= $forum['posts'];
				$forumlist[$forum['fup']]['todayposts'] += $forum['todayposts'];
			
			}
		}	
		
			unset($forum_access, $forum_fields);
		foreach($forumlist as $k=>$forum){
			$data_forum[$forum["fup"]][]=array(
					"board_id"			=>(int)$forum["fid"], 
					"board_name"		=>$forum["name"],
					"td_posts_num"		=>(int)$forum["todayposts"],
					"topic_total_num"	=>(int)$forum["threads"],
					"posts_total_num"	=>(int)$forum["posts"],	
					"last_posts_date"	=> $forum["lastpost"]['dateline'] = '0' ?'0' :$forum["lastpost"]['dateline'].'000',
			);
		}
		foreach($catlist as $k=>$cat){
			if(strstr(MOBCENBTYPE2,"|".$cat["fid"]."|")==""){
				$type=2;
			}else{
				$type=1;
			}
			$data_cat[]=array(
				"board_category_id"		=>(int)$cat["fid"],
				"board_category_name"	=>$cat["name"],
				"board_category_type"   =>$type,
				"board_list"			=>$data_forum[$cat["fid"]],
					
			);
		}	
        
		
		
		$number_obj = C::app()->session;
		$numbers=C::app()->session->count();
		$todaytime=strtotime(date("Y-m-d"));
		$tomorrowtime=strtotime(date("Y-m-d",strtotime("+1 day")));
		$N=DB::result_first('SELECT COUNT(*) FROM %t where lastolupdate between %s and %s', array('common_session',$todaytime,$tomorrowtime));
		$retarry = array($numbers, $N , $data_cat);
		return $retarry;
			}
		
	public function getclassified(){
		$uselessbids = $usingbids = $bids = array();
		$bids = C::t('common_block')->fetch_all_bid_by_blocktype(0,1000);
		$usingbids = array_keys(C::t('common_template_block')->fetch_all_by_bid($bids));
		$uselessbids = array_intersect($bids, $usingbids);
		$uselessbids =implode(',',$uselessbids);
		global $_G;
		block_get($uselessbids);
		return $uselessbids;
	}	
}		
			$xm=new topic();
			$s=file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
			$result =$xm->xml_to_array($s); 
		for($tm=0;$tm<count($result['board']['fid']);$tm++){
			$arrs[]=$result['board']['fid'][$tm][0];
		}	
		for($pm=0;$pm<count($result['pboard']['pfid']);$pm++){
			$parrs[]=$result['pboard']['pfid'][$pm][0];
		}
		
		$rst = array();
		foreach($result[category] as $rk=>$rc){
			foreach ($rc as $keys => $value) {
				$rst[$keys][$rk] = $value[0];
			}
		}
		foreach($rst as $rt){
			$catArr[$rt[cid]]=$rt;
		}
		 
		$flg=$result['login'][0][0];
		$faw=$result['allowReg'][0][0];
		$wbs=$result['weiboshow'][0][0];
		$qqs=$result['qqshow'][0][0];
		$frg=$result['register'][0][0];
		$fq=$result['wbkey'][0][0];
		$fsc=$result['secret'][0][0];
		$a = new manageImpl_x25();
		$list=$a->getManageObj();
		//print_r($list);exit;
		$classList = $a->getclassified();
		$classList = explode(',',$classList);
		foreach($result['classFid']['classItem'] as $val)
		{
			$classItem[]=!empty($val) && !is_array($val)?$val:$val[0];
		}
		$is_door = file_get_contents('install/moduledoor.log');
?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title></title>
	<link rel="stylesheet" href="images/anmi2discuse.css" />
	<link rel="stylesheet" href="images/anmi2discuse.css" />
	<link rel="stylesheet" type="text/css" href="wbox/wbox.css" />
	<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
	<script type="text/javascript" src="wbox/mapapi.js"></script> 
	<script type="text/javascript" src="wbox/wbox.js"></script> 
	<script type="text/javascript" src="images/check.js"></script> 
</head>
<body style="margin:0;padding:0;" onload="checkdiscuz1(<?php echo $result['version'][0][0]?>)">
<?php require_once 'top.php';?>
	<form id="discuse" action="submit.php" name="check_bottom" method ="post" onsubmit="return checksub(check_bottom)" style='padding-top:70px;'>
<?php 
if($is_door==1)
{?>
	<div style='color: #646464;font-size:12px;'>
	<div id='discuz_check' >
		<div class="discuse_content" >
			<input type='hidden' value='<?php echo $result['version'][0][0];?>' name='checkDiscuz'/>
		<div style="height:21px;margin-bottom:10px;">
			<span class="discuse_content_title"><?php echo Common::get_web_unicode_charset('\u95e8\u6237\u5e7b\u706f\u7ba1\u7406')?></span>
			<span class="discuse_content_sectionspan"><?php echo Common::get_web_unicode_charset('\u0028\u6700\u591a\u53ef\u6dfb\u52a0\u0035\u5f20\u5e7b\u706f\u7247\u0029')?></span>
		</div>
		<a href="huandeng/image.php" class="discuse_content_moduleslink" id="isFrameImg">+&nbsp;<?php echo Common::get_web_unicode_charset('\u70b9\u51fb\u6dfb\u52a0\u5e7b\u706f\u7247')?></a>
		<ul class="discuse_content_ulimg">
		<?php 
			$ImgArr = add_portal_module::check_module_isimage(1);
			$ImgArrSum = add_portal_module::check_module_isimage_count(1);
		?>
			<input type="hidden" value='<?php echo $ImgArrSum[0]['num'];?>' id="hiddenimgSumValue"/>
		<?php 
			foreach($ImgArr as $key=>$val)
			{
			
		?>
			<li>
				<div class="discuse_content_show">
				<?php 
				
					if(isset($val['imgval']) && !empty($val['imgval']) &&!is_numeric($val['imgval']))
					{
						echo '<img src='.$val['imgval'].' alt="" />';
					}else{
						echo '<img src=images/bg_fenlei.png alt="" />';
					}
				?>
					 
					<a href="#" title="<?php echo Common::get_web_unicode_charset($val['title']); ?>"><?php echo sub_str(Common::get_web_unicode_charset($val['title']),0,16); ?></a>
				     </div>
				
				<div class="discuse_content_a">
					<a href="huandeng/imageEdit.php?id=<?php echo $val['id'];?>" class="discuse_content_left"  ><?php echo Common::get_web_unicode_charset('\u7f16\u8f91')?></a>
					<a class="discuse_content_right" href="huandeng/imageDelete.php?id=<?php echo $val['id'];?>" onclick='return delconfirm()'><?php echo Common::get_web_unicode_charset('\u5220\u9664');?></a>
					<?php if($val['cidtype']=="aid"){ 
						$chkAid = DB::fetch(DB::query("SELECT aid FROM ".DB::table('portal_article_title')." WHERE aid=".(Int)$val['cid']));
						if(empty($chkAid)){
							echo '<span class="discuse_content_left" style="color: red">'.Common::get_web_unicode_charset("\u005b\u65e0\u6548\u005d").'</span>';
						}
					}elseif($val['cidtype']=="tid"){ 
						$chkTid = DB::fetch(DB::query("SELECT tid FROM ".DB::table('forum_thread')." WHERE tid=".(Int)$val['cid']));
						if(empty($chkTid)){
							echo '<span class="discuse_content_left" style="color: red">'.Common::get_web_unicode_charset("\u005b\u65e0\u6548\u005d").'</span>';
						}
					} 
					 
					?>
					 
				</div>
			</li>
		<?php 
			
		}
		?>
		
		</ul>
		<div style="clear:both;"></div>
	</div>
	<div class="discuse_content">
		<div style="height:21px;margin-bottom:10px;">
			<span class="discuse_content_title"><?php echo Common::get_web_unicode_charset('\u95e8\u6237\u8d44\u8baf\u7ba1\u7406')?></span>
			<span class="discuse_content_sectionspan"><?php echo Common::get_web_unicode_charset('\u0028\u6700\u591a\u53ef\u6dfb\u52a0\u0036\u4e2a\u8d44\u8baf\u5206\u7c7b\u0029')?></span>
		</div>
		<a href="module/module.php" class="discuse_content_moduleslink"  id="isFrame">+&nbsp;<?php echo Common::get_web_unicode_charset('\u70b9\u51fb\u6dfb\u52a0\u8d44\u8baf\u5206\u7c7b')?></a>
		<ul class="discuse_content_ulimg">
		<?php 
			$ImgArr = add_portal_module::check_module();
			$ImgArrSum = add_portal_module::check_module_sum();
			?>
			<input type="hidden"  value='<?php echo $ImgArrSum[0]['num'];?>' id="hiddenmoduleSumValue"/>
			<?php 
			foreach($ImgArr as $key=>$val)
			{
			
		?>
			<li>
				<div class="discuse_content_show">
					<img src="images/bg_fenlei.png" alt="" />
					<a href="module/moduleList.php?mid=<?php echo $val['id'];?>" title="<?php echo Common::get_web_unicode_charset($val['mname']); ?>"><?php echo sub_str(Common::get_web_unicode_charset($val['mname']),0,16);//?></a>
				</div>
				<div class="discuse_content_b">
				
					<a href='module/moduleEdit.php?id=<?php echo $val['id'];?>' class="discuse_content_left_b" ><?php echo Common::get_web_unicode_charset('\u7f16\u8f91')?></a>
					<a class="discuse_content_right_b" href="module/moduleDelete.php?id=<?php echo $val['id'];?>" onclick='return delconfirm()'><?php echo Common::get_web_unicode_charset('\u5220\u9664')?></a>
				</div>
			</li>
		<?php 
			
		}
		?>
		</ul>
		
	</div>
	</div>
	
		<?php }?>
		<div class="discuse_content"> 
			<div style="height:21px;">
				<span class="discuse_content_sectiontitle"><?php echo Common::get_web_unicode_charset('\u7248\u5757\u7ba1\u7406');?></span>
				<span class="discuse_content_sectionspan"><?php echo Common::get_web_unicode_charset('\uff08\u7ba1\u7406\u8bba\u575b\u7248\u5757\u662f\u5426\u663e\u793a\u5728\u5ba2\u6237\u7aef\uff0c\u9009\u62e9\u5373\u663e\u793a\u5728\u5ba2\u6237\u7aef\uff09');?>
					
				</span>
			</div>
			<ul class="discuse_contetn_option">
					<?php foreach($list[2] as $k){ ?> 
				<?php for($i=0;$i<count($list[2]);$i++){ ?>
				<?php if($list[2][$i][board_category_id]==$k[board_category_id]){?>	 
				<?php foreach($list[2][$i][board_list] as $kk){ ?>
					<li><?php $Uname = unicode_encode($kk['board_name']);
					/*$json = echo_mysql_json($kk);$barr = explode(',',$json);$Newbarr= explode(':',$barr[1]);$arr = echo_array($json);*/?>
						<input type="checkbox" name="fid[]" id="chk2" class="check" <?php if(in_array($kk[board_id],$arrs) || count($result)==0){echo "checked";} ?> value='<?php echo $kk[board_id].'@bordername@'.$list[2][$i][board_category_id]; ?>'/>
						<span><?php echo Common::get_web_unicode_charset($Uname);?></span>
					</li>
				<?php }}}} ?>
			</ul>
			<div class="checkall">
				<input type="checkbox" class="check" id="chk2" onclick="selectAll2(this);" <?php if(count($result)==0){echo "checked";} ?>/>
			<span><?php echo Common::get_web_unicode_charset('\u5168\u9009')?></span>
			</div>
		</div>
		
		<div class="discuse_content"> 
			<div style="height:21px;">
				<span class="discuse_content_sectiontitle"><?php echo Common::get_web_unicode_charset('\u677f\u5757\u56fe\u7247\u662f\u5426\u663e\u793a ');?></span>
				</span>
			</div>
			<ul class="discuse_contetn_option">
					<?php foreach($list[2] as $k){ ?> 
				<?php for($i=0;$i<count($list[2]);$i++){ ?>
				<?php if($list[2][$i][board_category_id]==$k[board_category_id]){?>	 
				<?php foreach($list[2][$i][board_list] as $kk){ ?>
					<li><?php $Uname = unicode_encode($kk['board_name']);
					/*$json = echo_mysql_json($kk);$barr = explode(',',$json);$Newbarr= explode(':',$barr[1]);$arr = echo_array($json);*/?>
						<input type="checkbox" name="pfid[]" class="check" id="chk1" <?php if(in_array($kk[board_id],$parrs) || count($result)==0){echo "checked";} ?> value='<?php echo $kk[board_id].'@bordername@'.$list[2][$i][board_category_id]; ?>'/>
						<span><?php echo Common::get_web_unicode_charset($Uname);?></span>
					</li>
				<?php }}}} ?>
			</ul>
			<div class="checkall">
				<input type="checkbox" class="check" id="chk1" onclick="selectAll(this);" <?php if(count($result)==0){echo "checked";} ?>/>
			<span><?php echo Common::get_web_unicode_charset('\u5168\u9009')?></span>
			</div>
		</div>
		
		<div class="discuse_content"> 
			<div style="height:21px;">
				<span class="discuse_content_sectiontitle"><?php echo Common::get_web_unicode_charset('\u677f\u5757\u663e\u793a\u6837\u5f0f');?></span>
			</div>
			<ul class="discuse_contetn_option">
				<?php foreach($list[2] as $k){ ?> 
				<?php for($i=0;$i<count($list[2]);$i++){ ?>
				<?php if($list[2][$i][board_category_id]==$k[board_category_id]){ 
					foreach($catArr as $ca){
						if($ca[cid]==$k[board_category_id]){
							if($ca[ctype]==1) $catFlag=1; else $catFlag=2;
						} 
					}?>	 
				 	<li> 
						<span><strong><?php echo replaceHtmlAndJs($k[board_category_name]); ?></strong></span>
					</li>
					<li> 
						<input type="radio" name="category<?php echo $i?>" class="check" <?php if($catFlag==2 || count($result)==0){echo "checked";} ?>
						 value='<?php echo $k[board_category_id].'@'.replaceHtmlAndJs($k[board_category_name]).'@2'; ?>'/>
						<span><?php echo Common::get_web_unicode_charset('\u53cc\u5217\u663e\u793a'); ?></span>
					</li>
					<li>  
						<input type="radio" name="category<?php echo $i?>" class="check" <?php if($catFlag==1){echo "checked";} ?>
						 value='<?php echo $k[board_category_id].'@'.replaceHtmlAndJs($k[board_category_name]).'@1'; ?>'/>
						<span><?php echo Common::get_web_unicode_charset('\u5355\u5217\u663e\u793a'); ?></span>
					</li>
					
				<?php }}} ?>
			</ul>
			<div class="checkall">
				 <input type="hidden" name="counti" value="<?php echo $i;?>" />
			</div>
		</div>
		
		<div class="discuse_content">
			<span class="discuse_content_moduletitle"><?php echo Common::get_web_unicode_charset('\u767b\u9646\u6b21\u6570\u7ba1\u7406')?></span>
			<div style="height:24px;margin-top:10px;">
				<span class="discuse_content_land"><?php echo Common::get_web_unicode_charset('\u540c\u4e00\u5ba2\u6237\u7aef\u6bcf\u5c0f\u65f6\u767b\u9646')?></span>
				<input class="discuse_content_times" type="text" name="login_count" value="<?php echo count($result)==0?"5":$flg; ?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
				<span class="discuse_content_land"><?php echo Common::get_web_unicode_charset('\u6b21\uff08\u0031\u002d\u0039\u0039\u0039\u0039\uff09')?></span>
			</div>
		</div>
		
		<div class="discuse_content">
			<div style="height:24px;margin-top:10px;">
				<div class="discuse_content_enroll">
				<span><?php echo Common::get_web_unicode_charset('\u5141\u8bb8\u6ce8\u518c')?></span>
				<input type="radio" name="isreg" value="1" <?php if($faw==1 || count($result)==0){echo "checked";}?> onClick="show(this)"/></div>
				
				<div class="discuse_content_noenroll">
				<span><?php echo Common::get_web_unicode_charset('\u4e0d\u5141\u8bb8\u6ce8\u518c')?></span>
				<input type="radio" name="isreg" value="0" <?php if($faw==0 && count($result)!=0){echo "checked";}?> onClick="show(this)"/></div>
			</div>
		</div>
		
		<div class="discuse_content" id="zhucecishu" style="display:<?php echo $faw=="1"  || count($result)==0?"block":"none"; ?>">
			<span class="discuse_content_moduletitle"><?php echo Common::get_web_unicode_charset('\u6ce8\u518c\u6b21\u6570\u7ba1\u7406')?></span>
			<div style="height:24px;margin-top:10px;">
				<span class="discuse_content_land"><?php echo Common::get_web_unicode_charset('\u540c\u4e00\u5ba2\u6237\u7aef\u6bcf\u5c0f\u65f6\u6ce8\u518c')?></span>
				<input class="discuse_content_times" type="text" name="register_count" value="<?php echo count($result)==0?"5":$frg; ?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/><span class="discuse_content_land"><?php echo Common::get_web_unicode_charset('\u6b21\uff08\u0031\u002d\u0039\u0039\u0039\u0039\uff09')?></span>
			</div>
		</div>
		
		<div class="discuse_content">
			<div style="height:24px;margin-top:10px;">
				<div class="discuse_content_enroll" style="width:100px">
				<span><?php echo Common::get_web_unicode_charset('\u5141\u8bb8\u0051\u0051\u767b\u5f55')?></span>
				<input type="radio" name="qqshow" value="1" <?php if($qqs==1 || count($result)==0){echo "checked";}?>/></div>
				
				<div class="discuse_content_noenroll">
				<span><?php echo Common::get_web_unicode_charset('\u4e0d\u5141\u8bb8\u0051\u0051\u767b\u5f55')?></span>
				<input type="radio" name="qqshow" value="0" <?php if($qqs==0 && count($result)!=0){echo "checked";}?>/></div>
			</div>
		</div>	
		
		<div class="discuse_content">
			<div style="height:24px;margin-top:10px;">
				<div class="discuse_content_enroll" style="width:100px">
				<span><?php echo Common::get_web_unicode_charset('\u5141\u8bb8\u5fae\u535a\u767b\u5f55')?></span>
				<input type="radio" name="weiboshow" value="1" <?php if($wbs==1 || count($result)==0){echo "checked";}?> onClick="rxweiboshow(this)"/></div>
				
				<div class="discuse_content_noenroll">
				<span><?php echo Common::get_web_unicode_charset('\u4e0d\u5141\u8bb8\u5fae\u535a\u767b\u5f55')?></span>
				<input type="radio" name="weiboshow" value="0" <?php if($wbs==0 && count($result)!=0){echo "checked";}?> onClick="rxweiboshow(this)"/></div>
			</div>
		</div>	
		
		<div class="discuse_content" style="height:60px;" id="weibologin" style="display:<?php echo $wbs=='1'  || count($result)==0?'block':'none'; ?>">
			<div style="float:left;height:40px;width:480px;">
				<div style="height:24px;">
					<span class="discuse_content_QQ"><?php echo Common::get_web_unicode_charset('\u8f93\u5165\u0057\u0065\u0069\u0062\u006f\u0020\u004b\u0065\u0079\u503c')?></span>
					<span class="discuse_content_qq"><?php echo Common::get_web_unicode_charset('\uff08\u652f\u6301\u5fae\u535a\u767b\u5f55\u4f7f\u7528\uff09')?></span>
				</div>
				<div class="discuse_content_qqs">
					<span>Weibo Key</span>
					<input type="text" name="wbkey" value="<?php echo $fq; ?>"/>
				</div>
			</div>
			<div  style="float:right;height:40px;width:480px;">
				<div style="height:24px;">
					<span class="discuse_content_QQ"><?php echo Common::get_web_unicode_charset('\u8f93\u5165\u0057\u0065\u0069\u0062\u006f\u0020\u0053\u0065\u0063\u0072\u0065\u0074\u503c')?></span>
					<span class="discuse_content_qq"><?php echo Common::get_web_unicode_charset('\uff08\u652f\u6301\u5fae\u535a\u767b\u5f55\u4f7f\u7528\uff09')?></span>
				</div>
				<div class="discuse_content_qqs">
					<span>Weibo secret</span>
					<input type="text" name="secret" value="<?php echo $fsc; ?>"/>
				</div>
			</div>
		</div>
		<div class="discuse_button">
			<div class="discuse_buttons">
				<input type="submit" class="confirm" value ='' style="cursor:pointer"/>
			</div>
		</div>
	</form>
</body>
<script type="text/javascript">
function checkdiscuz1(str)
{
	if(str ==1)
	{
		document.getElementById("discuse_content").style.display="block";
		document.getElementById("discuz_check").style.display="none";
	}else{
		document.getElementById("discuse_content").style.display="none";
		document.getElementById("discuz_check").style.display="block";
		}
}
function delconfirm(){
	  if(window.confirm(decodeURI('%E7%A1%AE%E5%AE%9A%E5%88%A0%E9%99%A4%EF%BC%9F'))){
	   return true;
	  }
	  return false;
	}
var str=$("#hiddenmoduleSumValue").val();
var img=$("#hiddenimgSumValue").val();
$(document).ready(function(){
	$("#isFrameImg").click(function(){
		if(img >=5)
		{

			alert(decodeURI('%E5%B9%BB%E7%81%AF%E7%89%87%E6%9C%80%E5%A4%9A%E4%B8%BA5%E4%B8%AA%EF%BC%8C%E8%AF%B7%E5%88%A0%E9%99%A4%E4%B9%8B%E5%90%8E%E5%86%8D%E6%B7%BB%E5%8A%A0%EF%BC%81'));
			return false;
		}
	});
	$("#isFrame").click(function(){
		if(str >=6)
		{
			alert(decodeURI('%E9%97%A8%E6%88%B7%E8%B5%84%E8%AE%AF%E6%9C%80%E5%A4%9A%E4%B8%BA6%E4%B8%AA%EF%BC%8C%E8%AF%B7%E5%88%A0%E9%99%A4%E5%90%8E%E5%86%8D%E6%B7%BB%E5%8A%A0%EF%BC%81'));
			return false;
		}
	});
	 
	 }); 
  </script>
</html>

<?php 
}else{
	echo "<script>location.href='login/login.php';</script>";
}
?>