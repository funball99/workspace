<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('PostPermImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('PostPermImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getPostPermObj();
$obj->transfer($retObj);
?>