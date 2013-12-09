<?php
require_once '../tool/tool.php';
if(file_exists('predefined.log'))
{
	$version = file_get_contents('predefined.log');
}else {
	$version =0;
}
$data['version'] =$version;
echo echo_json($data);
?>