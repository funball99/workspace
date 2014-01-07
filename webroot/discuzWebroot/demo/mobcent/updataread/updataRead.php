<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('updataReadImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('updataReadImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$img = $classObj->getUpdataReadObj();
$classObj->transfer($img);
?>