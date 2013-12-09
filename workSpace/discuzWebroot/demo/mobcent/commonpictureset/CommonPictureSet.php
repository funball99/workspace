<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('CommonPictureSetImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('CommonPictureSetImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getCommonPictureSetObj();
$obj->transfer($retObj);
?>