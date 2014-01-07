<?php
//get board List
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('commonLocationImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('commonLocationImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getCommonLocationObj();
$obj->transfer($retObj);
?>

