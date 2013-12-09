<?php 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
@set_magic_quotes_runtime(0);
ob_end_clean ();
require_once '../../Config/public.php';
require_once '../../public/mobcentDatabase.php';
require_once '../../../source/class/class_core.php';
require_once '../../../config/config_ucenter.php';
require 'include/install_mysql.php';
require 'include/install_var.php';
require_once '../tools.php';
C::app ()->init(); 

	$setting = array();
	$query = mysql_query("SELECT * FROM ".DB::table("common_setting"));
	while($row = mysql_fetch_array($query)) {
		$setting[$row['skey']] = $row['svalue'];
	}
	/*thread*/
	$thread_sql="SELECT COUNT(*) as num  FROM ".DB::table("forum_thread");
	$thread_query = mysql_query($thread_sql);
	$thread_num =mysql_fetch_array($thread_query); 
	/*post*/
	$post_sql="SELECT COUNT(*) as num  FROM ".DB::table("forum_post");
	$post_query = mysql_query($post_sql);
	$post_num =mysql_fetch_array($post_query);
	/*person*/
	$person_sql="SELECT count(uid) as num  FROM ".DB::table("common_member");
	$person_query = mysql_query($person_sql);
	$person_num =mysql_fetch_array($person_query);
	/*online*/
	$online_sql="SELECT data  FROM ".DB::table("common_syscache")." where cname ='onlinerecord'";
	$online_query = mysql_query($online_sql);
	$online_num =mysql_fetch_array($online_query);
	$onlineinfo = explode("\t", $online_num['data']);
	/*user pwd*/
	$pwd_arr = DB::fetch(DB::query("SELECT * FROM ".DB::table('add_admin')." where id = 1"));
	$userpwd=$pwd_arr['password'];
	$pwd = md5(trim($_REQUEST['Apk_pack_pwd']));
	if($pwd == $userpwd)
	{ 
		$data['info'] = array(
				'setting_basic_bbname' =>$setting['bbname'],
				'setting_basic_sitename' =>$setting['sitename'],
				'setting_basic_siteurl' =>$setting['siteurl'],
				'setting_basic_adminemail' => $setting['adminemail'],
				'setting_basic_icp' => $setting['icp'],
				'setting_basic_boardlicensed' => $setting['boardlicensed'],
				'setting_basic_stat' =>  $setting['statcode'],
				'onlineinfo' =>  empty($onlineinfo[0])?0:$onlineinfo[0],
				'thread_num' =>  $thread_num['num'],
				'post_num' =>  $post_num['num'] - $thread_num['num'],
				'person_num' => $person_num['num']
		);
		$data['rs'] =1;
	}else
	{
		echo '{"rs":0,"errcode":01010000}';exit();
	}
	echo echo_json($data);
?>