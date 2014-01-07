<?php 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
@set_magic_quotes_runtime(0);

define('IN_DISCUZ', TRUE);
define('IN_COMSENZ', TRUE);
define('ROOT_PATH', dirname(__FILE__).'/../');
define('CONFIG', '../config/config_global.php');
require '../../install/include/install_var.php';
require '../../install/include/install_mysql.php';
require ROOT_PATH.'../install/include/install_function.php';


if(file_exists(ROOT_PATH.CONFIG)) {
	include ROOT_PATH.CONFIG;
} else {
	$_config = $default_config;
}
$dbhost = $_config['db'][1]['dbhost'];
$dbname = $_config['db'][1]['dbname'];
$dbpw = $_config['db'][1]['dbpw'];
$dbuser = $_config['db'][1]['dbuser'];
$tablepre = $_config['db'][1]['tablepre'];
$dbname_not_exists = true;

$db = new dbstuff;

$db->connect($dbhost, $dbuser, $dbpw, $dbname, DBCHARSET);

$sql = file_get_contents(ROOT_PATH.'./install/data/install_surring.sql');
$sql = str_replace("\r\n", "\n", $sql);
runquery($sql);

dirfile_check($dirfile_items);
?>