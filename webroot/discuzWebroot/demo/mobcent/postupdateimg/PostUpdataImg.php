<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('PostUpdataImgImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('PostUpdataImgImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$img = $classObj->getPostUpdataImgObj();
$classObj->transfer($img);
?>