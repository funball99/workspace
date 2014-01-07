<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('boardChildImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('boardChildImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$array = $classObj->getSubBoardList();
$classObj->transfer($array);
?>

