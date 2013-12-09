<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('savewebregisteImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('savewebregisteImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$res = $classObj->savewebregiste();
$classObj->transfer($res);
?>