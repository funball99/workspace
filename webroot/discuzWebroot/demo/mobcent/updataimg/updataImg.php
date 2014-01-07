<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('updataImgImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('updataImgImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$img = $classObj->updataImg();
$classObj->transfer($img);
?>