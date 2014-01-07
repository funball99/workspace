<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('registeImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('registeImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$res = $classObj->registe();
$classObj->transfer($res);
?>