<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('messageHeartImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('messageHeartImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getMessageHeartObj();
$obj->transfer($retObj);
?>

