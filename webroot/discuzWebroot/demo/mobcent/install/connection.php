<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
@set_magic_quotes_runtime(0);

define('IN_DISCUZ', TRUE);
define('IN_COMSENZ', TRUE);
define('ROOT_PATH', dirname(__FILE__).'/../');
define('CONFIG', '../config/config_global.php');
require 'include/install_mysql.php';
require 'include/install_var.php';
require_once '../tool/tool.php';
define('ROOT_PATH', dirname(__FILE__).'/../../');

	if(file_exists(ROOT_PATH.CONFIG)) {
		include ROOT_PATH.CONFIG;
	} else {
		$_config = $default_config;
	}
	$dbhost = $_config['db'][1]['dbhost'];
	$dbname = $_config['db'][1]['dbname'];
	$dbpw = $_config['db'][1]['dbpw'];
	$dbuser = $_config['db'][1]['dbuser'];
	define('DBCHARSET',$_config['db'][1]['dbcharset']);
	$tablepre = $_config['db'][1]['tablepre'];
	$dbname_not_exists = true;

	$db = new dbstuff;


	$db->connect($dbhost, $dbuser, $dbpw, $dbname, DBCHARSET);

	$setting = array();
	$query = mysql_query("SELECT * FROM ".$tablepre."common_setting");
	while($row = mysql_fetch_array($query)) {
		$setting[$row['skey']] = $row['svalue'];
	}

	/*thread*/
	$thread_sql="SELECT COUNT(*) as num  FROM ".$tablepre."forum_thread";
	$thread_query = mysql_query($thread_sql);
	$thread_num =mysql_fetch_array($thread_query);
	/*post*/
	$post_sql="SELECT COUNT(*) as num  FROM ".$tablepre."forum_post";
	$post_query = mysql_query($post_sql);
	$post_num =mysql_fetch_array($post_query);
	/*person*/
	$person_sql="SELECT count(uid) as num  FROM ".$tablepre."common_member";
	$person_query = mysql_query($person_sql);
	$person_num =mysql_fetch_array($person_query);
	
	$online_sql="SELECT data  FROM ".$tablepre."common_syscache where cname ='onlinerecord'";
	$online_query = mysql_query($online_sql);
	$online_num =mysql_fetch_array($online_query);
	$onlineinfo = explode("\t", $online_num['data']);
	$pwd = trim($_REQUEST['Apk_pack_pwd']);
	$xml = simplexml_load_file('AppPackPwd.xml');
	if($pwd == $xml->password)
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