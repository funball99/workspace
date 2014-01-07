<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('PlugCheckImpl').'.php'; 
$className = dynamicobject :: getShortDymanicObject('PlugCheckImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getPlugSignObj();
$obj->transfer($retObj);
?>