<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('SettingImpl').'.php'; 
$className = dynamicobject :: getShortDymanicObject('SettingImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getPlugSignObj();
$obj->transfer($retObj);
?>