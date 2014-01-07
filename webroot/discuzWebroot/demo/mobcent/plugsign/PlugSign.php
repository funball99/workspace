<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('PlugSignImpl').'.php'; 
$className = dynamicobject :: getShortDymanicObject('PlugSignImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getPlugSignObj();
$obj->transfer($retObj);
?>