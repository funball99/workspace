<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('postHeartImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('postHeartImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getPostHeartObj();
$obj->transfer($retObj);
?>

