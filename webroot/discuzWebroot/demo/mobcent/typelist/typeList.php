<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('typeListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('typeListImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$array = $classObj->getSubBoardList();
$classObj->transfer($array);
?>

