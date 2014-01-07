<?php 
//get board List //hehe...just test...
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('boardListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('boardListImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getBoardListObj();
$obj->transfer($retObj);
?>

