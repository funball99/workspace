<?php 
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../../source/function/function_forumlist.php';
require_once '../tool/tool.php';
require_once '../model/table/x20/topic.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
 
define('IN_MOBCENT',1);
require_once '../model/table/x20/mobcentDatabase.php';
require_once '../install/checkModule.php';

class manageImpl_x20{ 
	public function getManageObj() {
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
		setglobal('groupid', $group['groupid']);
		global $_G;
		
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		
		 $space = $info->getUserInfo(intval(($arrAccess['user_id'])));		
		
		$gid = intval(getgpc('gid'));
		$catlist = $forumlist = $sublist = $forumname = $collapse = $favforumlist = array();
		if(!$gid) {
			$sql = !empty($_G['member']['accessmasks']) ?
			"SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.inheritedmod, f.domain,
					f.forumcolumns, f.simple, ff.description, ff.moderators, ff.icon, ff.viewperm, ff.redirect, ff.extra, a.allowview
					FROM ".DB::table('forum_forum')." f
					LEFT JOIN ".DB::table('forum_forumfield')." ff ON ff.fid=f.fid
					LEFT JOIN ".DB::table('forum_access')." a ON a.uid='$_G[uid]' AND a.fid=f.fid
						WHERE f.status='1' ORDER BY f.type, f.displayorder"
						: "SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.inheritedmod, f.domain,
					f.forumcolumns, f.simple, ff.description, ff.moderators, ff.icon, ff.viewperm, ff.redirect, ff.extra
					FROM ".DB::table('forum_forum')." f
					LEFT JOIN ".DB::table('forum_forumfield')." ff USING(fid)
					WHERE f.status='1' ORDER BY f.type, f.displayorder";
			
			$query = DB::query($sql);
			while($forum = DB::fetch($query)) {
				
				$forumname[$forum['fid']] = strip_tags($forum['name']);
				$forum['extra'] = unserialize($forum['extra']);
				if(!is_array($forum['extra'])) {
					$forum['extra'] = array();
				}
			
				if($forum['type'] != 'group') {
			
					$threads += $forum['threads'];
					$posts += $forum['posts'];
					$todayposts += $forum['todayposts'];
			
					if($forum['type'] == 'forum' && isset($catlist[$forum['fup']])) {
						if(forum($forum)) {
							$catlist[$forum['fup']]['forums'][] = $forum['fid'];
							$forum['orderid'] = $catlist[$forum['fup']]['forumscount']++;
							$forum['subforums'] = '';
							$forumlist[$forum['fid']] = $forum;
						}
						$arr[]=$forumlist;
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
			if(!IS_ROBOT && ($_G['setting']['whosonlinestatus'] == 1 || $_G['setting']['whosonlinestatus'] == 3)) {
				$_G['setting']['whosonlinestatus'] = 1;
			
				$onlineinfo = explode("\t", $_G['cache']['onlinerecord']);
				if(empty($_G['cookie']['onlineusernum'])) {
					$onlinenum = DB::result_first("SELECT count(*) FROM ".DB::table('common_session'));
					if($onlinenum > $onlineinfo[0]) {
						$onlinerecord = "$onlinenum\t".TIMESTAMP;
						DB::query("UPDATE ".DB::table('common_setting')." SET svalue='$onlinerecord' WHERE skey='onlinerecord'");
						save_syscache('onlinerecord', $onlinerecord);
						$onlineinfo = array($onlinenum, TIMESTAMP);
					}
					dsetcookie('onlineusernum', intval($onlinenum), 300);
				} else {
					$onlinenum = intval($_G['cookie']['onlineusernum']);
				}
				$onlineinfo[1] = dgmdate($onlineinfo[1], 'd');
			
				$detailstatus = $showoldetails == 'yes' || (((!isset($_G['cookie']['onlineindex']) && !$_G['setting']['whosonline_contract']) || $_G['cookie']['onlineindex']) && $onlinenum < 500 && !$showoldetails);
			
				if($detailstatus) {
					$actioncode = lang('action');
			
					$_G['uid'] && updatesession();
					$membercount = $invisiblecount = 0;
					$whosonline = array();
			
					$_G['setting']['maxonlinelist'] = $_G['setting']['maxonlinelist'] ? $_G['setting']['maxonlinelist'] : 500;
					$query = DB::query("SELECT uid, username, groupid, invisible, lastactivity, fid FROM ".DB::table('common_session')." WHERE uid>'0' LIMIT ".$_G['setting']['maxonlinelist']);
					while($online = DB::fetch($query)) {
						$membercount ++;
						if($online['invisible']) {
							$invisiblecount++;
							continue;
						} else {
							$online['icon'] = !empty($_G['cache']['onlinelist'][$online['groupid']]) ? $_G['cache']['onlinelist'][$online['groupid']] : $_G['cache']['onlinelist'][0];
						}
						$online['lastactivity'] = dgmdate($online['lastactivity'], 't');
						$whosonline[] = $online;
					}
					if(isset($_G['cache']['onlinelist'][7]) && $_G['setting']['maxonlinelist'] > $membercount) {
						$query = DB::query("SELECT uid, username, groupid, invisible, lastactivity, fid FROM ".DB::table('common_session')." WHERE uid='0' ORDER BY uid DESC LIMIT ".($_G['setting']['maxonlinelist'] - $membercount));
						while($online = DB::fetch($query)) {
							$online['icon'] = $_G['cache']['onlinelist'][7];
							$online['username'] = $_G['cache']['onlinelist']['guest'];
							$online['lastactivity'] = dgmdate($online['lastactivity'], 't');
							$whosonline[] = $online;
						}
					}
					unset($actioncode, $online);
					$onlinenum =780;
					$db = DB::object();
					$db->free_result($query);
					unset($online);
				}
			
			} else {
				$_G['setting']['whosonlinestatus'] = 0;
			}
		
		}	
		foreach($forumlist as $k=>$forum){
			$part = "/.*?<span.*?title=\"(.*?)\"/i";
			$res=preg_match_all($part, $forum["lastpost"]['dateline'],$match);
			$data_forum[$forum["fup"]][]=array(
					"board_id"			=>(int)$forum["fid"], 
					"board_name"		=>preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$forum["name"]),
					"td_posts_num"		=>(int)$forum["todayposts"],
					"topic_total_num"	=>(int)$forum["threads"],
					"posts_total_num"	=>(int)$forum["posts"],		
					"last_posts_date"	=> strtotime($match[1][0]).'000',
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
				"board_list"			=>empty($data_forum[$cat["fid"]])?array():$data_forum[$cat["fid"]],	 
			);
		}	
		$number_obj = $discuz->session;
		$numbers=DB::result_first("SELECT COUNT(*) FROM ".DB::table('common_session')." WHERE invisible = '1'");
		$todaytime=strtotime(date("Y-m-d"));
		$tomorrowtime=strtotime(date("Y-m-d",strtotime("+1 day")));
		$N=DB::result_first('SELECT COUNT(*) FROM '.DB::table('common_session').' where lastolupdate between '.$todaytime.' and '.$tomorrowtime);
		$data = array($numbers,$N,$data_cat);
		$info = array(
				'online_user_num'	=>$data[0],
				'td_visitors'		=>$data[1],
				'list'				=>$data[2],
				"img_url"			=>"",
				"rs"				=>1
		);
		return $info;
			}
		
	public function getclassified(){
			$uselessbids = $usingbids = $bids = array();
			$query = DB::query("SELECT bid FROM ".DB::table('common_block')." WHERE blocktype='0' ORDER BY bid DESC LIMIT 1000");
			while($value = DB::fetch($query)) {
				$bids[] = intval($value['bid']);
			}
			$query = DB::query("SELECT bid FROM ".DB::table('common_template_block')." WHERE bid IN (".dimplode($bids).")");
			while(($value = DB::fetch($query))) {
				$usingbids[] = intval($value['bid']);
			}
			$uselessbids = array_intersect($bids, $usingbids);
			$uselessbids =implode(',',$uselessbids);
			global $_G;
			block_get($uselessbids);
		
			return $uselessbids;
	}	
}		
			$xm=new topic();
			$s=file_exists('../manage/App.xml')?join("",file('../manage/App.xml')):array();
			$result =$xm->xml_to_array($s); 
	 
		
		for($tm=0;$tm<count($result['board']['fid']);$tm++){
			$arrs[]=$result['board']['fid'][$tm][0][0];
		} 
		$flg=$result['login'][0][0][0];
		$faw=$result['allowReg'][0][0][0];
		$wbs=$result['weiboshow'][0][0][0];
		$qqs=$result['qqshow'][0][0][0];
		$frg=$result['register'][0][0][0];
		$fq=$result['wbkey'][0][0][0];
		$fsc=$result['secret'][0][0][0];
		
		$a=new manageImpl_x20();
		$list=$a->getManageObj();
		$classList = $a->getclassified();
		$classList = explode(',',$classList);
		foreach($result['classFid']['classItem'] as $val)
		{
			$classItem[]=!empty($val) && !is_array($val)?$val:$val[0][0];
		}
		$is_door = file_get_contents('../install/moduledoor.log');
		
		/*2013-06,men hu*/
		$newstype_list = DB::query("SELECT * FROM ".DB::table('add_module')." ORDER BY display desc LIMIT 6");
		while($value = DB::fetch($newstype_list)) {
			$newstype[] = $value;
		}
		$newstype_count_list = DB::query("SELECT count(*) as num FROM ".DB::table('add_module'));
		while($newstype_value = DB::fetch($newstype_count_list)) {
			$newstype_count[] = $newstype_value;
		}
		
		$image_list = DB::query("SELECT * FROM ".DB::table('add_portal_module')." where isimage=1 ORDER BY display desc LIMIT 5");
		while($image_value = DB::fetch($image_list)) {
			$image[] = $image_value;
		}		
		$image_count_list = DB::query("SELECT count(*) as num FROM ".DB::table('add_portal_module')." where isimage=1");
		while($image_count_value = DB::fetch($image_count_list)) {
			$image_count[] = $image_count_value;
		}
		/*--------------end men hu--------------*/
	
?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title></title>
	<link rel="stylesheet" href="images/anmi2discuse.css" />
	<link rel="stylesheet" type="text/css" href="wbox/wbox.css" />
	<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
	<script type="text/javascript" src="wbox/mapapi.js"></script> 
	<script type="text/javascript" src="wbox/wbox.js"></script> 
	<script type="text/javascript" src="images/check.js"></script> 
</head>
<body style="margin:0;padding:0;" onload="javascript:checkrenxing(<?php echo $result['version'][0][0][0]?>)"> 
<div style="z-index:1000;" id="head">
		<div style="padding-left:0px;width:900px;" id="head_nav">
			<div id="nav">
				<a href="/"><?php echo Common::get_web_unicode_charset('\u8fd4\u56de');?></a>
				<a class="selet" id="index" href=""><?php echo Common::get_web_unicode_charset('\u7ba1\u7406\u9875\u9762 ');?></a>
			</div>
		</div>
</div>



<form id="discuse" style='padding-top:70px;' action="submit.php" name="check_bottom" method ="post" >

<?php if($is_door ==1){ global $_G;?>
 	<div style='color: #646464;font-size:12px;padding-left:20px;padding-top:20px'>
	
	
	<div id='discuz_check' >
	<div class="discuse_content">	
	<input type='hidden' value='<?php echo $result['version'][0][0][0];?>' name='checkDiscuz'/> 
	
		<div style="height:21px;margin-bottom:10px;float:left">
			<span class="discuse_content_title"><?php echo Common::get_web_unicode_charset('\u95e8\u6237\u5e7b\u706f\u7ba1\u7406');?></span>
			<span class="discuse_content_sectionspan"><?php echo Common::get_web_unicode_charset('\uff08\u6700\u591a\u53ef\u6dfb\u52a0\u0035\u5f20\u5e7b\u706f\u7247\uff09');?></span>
		</div>
		<a href="image.php" class="discuse_content_moduleslink" id="huandeng">+&nbsp;<?php echo Common::get_web_unicode_charset('\u70b9\u51fb\u6dfb\u52a0\u5e7b\u706f\u7247');?></a>
		<input type="hidden" value='<?php echo $image_count[0]['num'];?>' id="hiddenimgSumValue"/>
		<ul class="discuse_content_ulimg">
		<?php for($my=0;$my<count($image);$my++){?>
			<li>
				<div class="discuse_content_show">
					<?php 
					if(isset($image[$my][imgval]) && !is_numeric($image[$my][imgval]))
					{
						echo '<img src='.$image[$my][imgval].' alt="" />';
					}else{
						echo '<img src=images/bg_fenlei.png alt="" />';
					}?>		 					 
					<a href="#" title="<?php echo Common::get_web_unicode_charset($image[$my][title]);?>">
					<?php echo sub_str(Common::get_web_unicode_charset($image[$my][title]),0,16);?></a>
				</div>
				<div class="discuse_content_a">
					 
					<a class="discuse_content_left" href="imageEdit.php?id=<?php echo $image[$my][id];?>&rd=<?php echo time();?>"><?php echo Common::get_web_unicode_charset('\u7f16\u8f91');?></a>
					<a class="discuse_content_right" href="imageSubmit.php?act=del&id=<?php echo $image[$my][id]; ?>" onclick='return delconfirm()'>
					<?php echo Common::get_web_unicode_charset('\u5220\u9664');?></a>
				</div>
			</li>	
			<?php }?>		 
		</ul>
		<div style="clear:both;"></div>
	</div>
	<div class="discuse_content">
		<div style="height:21px;margin-bottom:10px;">
			<span class="discuse_content_title"><?php echo Common::get_web_unicode_charset('\u95e8\u6237\u8d44\u8baf\u7ba1\u7406');?></span>
			<span class="discuse_content_sectionspan"><?php echo Common::get_web_unicode_charset('\uff08\u6700\u591a\u53ef\u6dfb\u52a0\u0036\u4e2a\u8d44\u8baf\u5206\u7c7b\uff09');?></span>
		</div>
		<a class="discuse_content_moduleslink" href="module.php" id="zixunfenlei">+&nbsp;<?php echo Common::get_web_unicode_charset('\u70b9\u51fb\u6dfb\u52a0\u8d44\u8baf\u5206\u7c7b');?></a>
		 
		 <input type="hidden" value='<?php echo $newstype_count[0]['num'];?>' id="hiddenmoduleSumValue"/>
		<ul class="discuse_content_ulimg">
			<?php for($hi=0;$hi<count($newstype);$hi++){?>
			<li>
				<div class="discuse_content_show">
					<img src="images/bg_fenlei.png" alt="" />
					<a href="news.php?typeid=<?php echo $newstype[$hi][id];?>" title="<?php echo Common::get_web_unicode_charset($newstype[$hi][mname]);?>"><?php echo sub_str(Common::get_web_unicode_charset($newstype[$hi][mname]),0,16);?></a>
				</div>
				<div class="discuse_content_a">
 				
					<a class="discuse_content_left_b" href="moduleEdit.php?id=<?php echo $newstype[$hi][id];?>&rd=<?php echo time();?> "><?php echo Common::get_web_unicode_charset('\u7f16\u8f91');?></a>
					<a class="discuse_content_right"  href="moduleSubmit.php?act=del&id=<?php echo $newstype[$hi][id];?>" onclick='return delconfirm()'>
					<?php echo Common::get_web_unicode_charset('\u5220\u9664');?></a>
				</div>
			</li>
			<?php }?>
		</ul>
		 
		
		</div>
	</div>
			<?php }?>
		
		<div class="discuse_content">
			<div style="height:21px;">
				<span class="discuse_content_sectiontitle"><strong><?php echo Common::get_web_unicode_charset('\u7248\u5757\u7ba1\u7406') ?></strong></span>
				<span class="discuse_content_sectionspan">
					<?php echo Common::get_web_unicode_charset('\uff08\u7ba1\u7406\u8bba\u575b\u7248\u5757\u662f\u5426\u663e\u793a\u5728\u5ba2\u6237\u7aef\uff0c\u9009\u62e9\u5373\u663e\u793a\u5728\u5ba2\u6237\u7aef\uff09')?>
				</span>
			</div>
			<ul class="discuse_contetn_option">
				<?php foreach($list['list'] as $k){ ?> 
				<?php for($i=0;$i<count($list['list']);$i++){ ?>
				<?php if($list['list'][$i][board_category_id]==$k[board_category_id]){ ?>	 
 
				<?php foreach($list['list'][$i][board_list] as $kk){ ?>
					<li><?php $Uname = unicode_encode($kk['board_name']);$json = echo_mysql_json($kk);$barr = explode(',',$json);$Newbarr= explode(':',$barr[1]);$arr = echo_array($json);?>
 
					 <input type="checkbox" name="fid[]" class="check" <?php if(in_array($kk[board_id],$arrs) || count($result)==0){echo "checked";} ?> value='<?php echo $kk[board_id].'@bordername@'.$list['list'][$i][board_category_id]; ?>'/>
						<span><?php echo Common::get_web_unicode_charset($Uname); ?></span>  
				
					</li>
				<?php }}}} ?>
			</ul>
			<div class="checkall">
			<input type="checkbox" class="check" onclick="selectAll(this);" <?php if(count($result)==0){echo "checked";} ?>/>
			<span><?php echo Common::get_web_unicode_charset('\u5168\u9009')?></span>
			</div>
		</div>
		
		<!--login-->
		<div class="discuse_content">
			<span class="discuse_content_moduletitle"><strong><?php echo Common::get_web_unicode_charset('\u767b\u9646\u6b21\u6570\u7ba1\u7406')?></strong></span>
			<div style="height:24px;margin-top:10px;">
				<span class="discuse_content_land"><?php echo Common::get_web_unicode_charset('\u540c\u4e00\u5ba2\u6237\u7aef\u6bcf\u5c0f\u65f6\u767b\u9646')?></span>
				<input class="discuse_content_times" type="text" name="login_count" value="<?php echo count($result)==0?"5":$flg; ?>" onkeyup="this.value=this.value.replace(/\D/g,'')" 
				onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
				<span class="discuse_content_land"><?php echo Common::get_web_unicode_charset('\u6b21\uff08\u0031\u002d\u0039\u0039\u0039\u0039\uff09')?></span>
			</div>
		</div>
		
		<!--regiest-->
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
			<span class="discuse_content_moduletitle"><strong><?php echo Common::get_web_unicode_charset('\u6ce8\u518c\u6b21\u6570\u7ba1\u7406')?></strong></span>
			<div style="height:24px;margin-top:10px;">
				<span class="discuse_content_land"><?php echo Common::get_web_unicode_charset('\u540c\u4e00\u5ba2\u6237\u7aef\u6bcf\u5c0f\u65f6\u6ce8\u518c')?></span>
				<input class="discuse_content_times" type="text" name="register_count" value="<?php echo count($result)==0?"5":$frg; ?>" onkeyup="this.value=this.value.replace(/\D/g,'')" 
				onafterpaste="this.value=this.value.replace(/\D/g,'')"/><span class="discuse_content_land"><?php echo Common::get_web_unicode_charset('\u6b21\uff08\u0031\u002d\u0039\u0039\u0039\u0039\uff09')?></span>
			</div>
		</div>
		
		<!--  
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
		-->
		
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
		
	 
		<div class="discuse_content" style="height:60px;">
			<div style="float:left;height:40px;width:480px;">
				<div style="height:24px;">
					<span class="discuse_content_QQ"><strong><?php echo Common::get_web_unicode_charset('\u8f93\u5165\u0057\u0065\u0069\u0062\u006f\u0020\u004b\u0065\u0079\u503c')?></strong></span>
					<span class="discuse_content_qq"><?php echo Common::get_web_unicode_charset('\uff08\u652f\u6301\u5fae\u535a\u767b\u5f55\u4f7f\u7528\uff09 ')?></span>
				</div>
				<div class="discuse_content_qqs">
					<span>Weibo Key</span>
					<input type="text" name="wbkey" value="<?php echo Common::get_web_unicode_charset($fq); ?>"/>
				</div>
			</div>
			<div  style="float:right;height:40px;width:480px;">
				<div style="height:24px;">
					<span class="discuse_content_QQ"><strong><?php echo Common::get_web_unicode_charset('\u8f93\u5165\u0057\u0065\u0069\u0062\u006f\u0020\u0053\u0065\u0063\u0072\u0065\u0074\u503c')?></strong></span>
					<span class="discuse_content_qq"><?php echo Common::get_web_unicode_charset('\uff08\u652f\u6301\u5fae\u535a\u767b\u5f55\u4f7f\u7528\uff09 ')?></span>
				</div>
				<div class="discuse_content_qqs">
					<span>Weibo secret</span>
					<input type="text" name="secret" value="<?php echo Common::get_web_unicode_charset($fsc); ?>"/>
				</div>
			</div>
		</div>
 
		<div class="discuse_button">
			<div class="discuse_buttons">
			<input type="submit" class="confirm" value ='' style="cursor:pointer"/>
			<input class="quit" type="reset" value ='' onclick="location.href='login.php?act=out'" style="cursor:pointer"/>
			</div>
		</div>
	</form>
<script type="text/javascript"> 
function checkrenxing(str)
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

var str=$("#hiddenmoduleSumValue").val();
var img=$("#hiddenimgSumValue").val();

$(document).ready(function(){
	$("#huandeng").click(function(){
		if(img >=5)
		{
			alert(decodeURI('%E5%B9%BB%E7%81%AF%E7%89%87%E6%9C%80%E5%A4%9A%E4%B8%BA5%E4%B8%AA%EF%BC%8C%E8%AF%B7%E5%88%A0%E9%99%A4%E4%B9%8B%E5%90%8E%E5%86%8D%E6%B7%BB%E5%8A%A0%EF%BC%81'));
			return false;
		}
		 
	});
	$("#zixunfenlei").click(function(){
		if(str >=6)
		{
			alert(decodeURI('%E9%97%A8%E6%88%B7%E8%B5%84%E8%AE%AF%E6%9C%80%E5%A4%9A%E4%B8%BA6%E4%B8%AA%EF%BC%8C%E8%AF%B7%E5%88%A0%E9%99%A4%E5%90%8E%E5%86%8D%E6%B7%BB%E5%8A%A0%EF%BC%81'));
			return false;
		}
		 
	});
	
	 
}); 
   
   $("#isFrame").wBox({requestType:"iframe",iframeWH:{width:470,height:350},target:"addPicHtml.php"});
   
   function delconfirm(){
	  if(window.confirm(decodeURI("%E7%A1%AE%E5%AE%9A%E5%88%A0%E9%99%A4%EF%BC%9F"))){
	  	return true;
	  }
	 	return false;
	}
	
</script>
</body>
</html>
