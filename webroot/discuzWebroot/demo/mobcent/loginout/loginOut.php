<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('loginOutImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('loginOutImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$obj = $classObj->getloginOutObj();
$classObj->transfer($obj);
?>