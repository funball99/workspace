<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('SendPostImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('SendPostImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getSendPostObj();
$obj->transfer($retObj);
?>