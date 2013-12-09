<?php

// include discuz params
$discuzParams = require_once('discuz.php');

// include constant define
require_once('constant.php');

// include database config
$dbConfig = require_once('database.php');

$mobcentConfig = require_once('mobcent.php');

if (YII_DEBUG) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL & ~E_NOTICE);
	error_reporting(E_ALL);
}