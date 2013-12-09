<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('msgReadImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('msgReadImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getMsgReadObj();
$obj->transfer($retObj);
?>

