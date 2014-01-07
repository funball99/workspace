<?php
require_once ('../../source/class/class_core.php');
require_once ('../../source/discuz_version.php');
require_once '../tool/tool.php';
$version = 'x25';
if (DISCUZ_VERSION == 'X2')
	$version = 'x20';
require_once '../model/table/'.$version.'/topic.php';

$topic = new topic();
if(!file_exists('../../data/attachment/appbyme')){
	$url =file_exists('../manage/App.xml')?join("",file('../manage/App.xml')):array();
}else{
	$url =file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
}

if(!empty($url))
{
	$xml = $topic->xml_to_array($url);
	if($xml['allowReg'][0][0]>0)
	{
		$data['rs'] =1;
	}else
	{
		$data['rs'] =0;
		$data['errcode'] ='03000002';
	}
}else{
	$data['rs'] =1;
}

echo echo_json($data);
?>