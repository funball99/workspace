<?php
/*
	[������׷����ظ�]Guisepost Plugin For Discuz! X1.0 - X3.1 ALL
	Copyring (C) KL3w.com; You can modify this plugin for your website
	This is not a freeware, use is subject to license terms
	Author: PGCAO��Version: 2.3.3��Time: 2013-10-26 14:10:41
*/
 
if(!defined('IN_DISCUZ')){exit('Access Denied');}

if($_G['inajax']){
	$uid = $_G['uid'];$use_uid = 0;$has_guisepostrate = 0;
	$tid = isset($_POST['tid']) ? intval($_POST['tid']) : intval($_G['gp_tid']);
	$pid = isset($_POST['pid']) ? intval($_POST['pid']) : intval($_G['gp_pid']);
	if($uid && $tid && $pid){
		loadcache('plugin'); //��ȡ�������
		$vars = $_G['cache']['plugin']['kl3w_guisepost'];		
		$rn = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? "\r\n" : "\n";
		$useidarr = explode($rn, $vars['useid']);
		if(is_array($useidarr) && $vars['open']){
			foreach($useidarr as $key => $val){
				$useval = explode("=", str_replace('@','=',$val));
				if($uid == $useval[0] && isset($useval[1]) && !empty($useval[1])){
					$use_uid = intval(trim($useval[0]));
					break;
				}
			}
		}		
		if($use_uid){
			$action = isset($_GET['action']) ? $_GET['action'] : $_G['gp_action'];
			if($action == 'rate'){
				$has_guisepostrate = @file_get_contents(DISCUZ_ROOT.'./source/module/forum/forum_misc.php');
				$has_guisepostrate = substr_count($has_guisepostrate,'guisepostuserid');
				$has_guisepostrate = $has_guisepostrate == 9 ? true : false;
			}
			$template = $action == 'rate' ? 'guiserate' : 'comment';
			include template('kl3w_guisepost:'.$template);
			exit(0);
		}
	}
}
showmessage('undefined_action', NULL);
?>