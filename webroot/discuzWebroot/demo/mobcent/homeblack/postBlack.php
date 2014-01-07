<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('postBlackImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('postBlackImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getBlackObj();
$obj->transfer($retObj);
?>