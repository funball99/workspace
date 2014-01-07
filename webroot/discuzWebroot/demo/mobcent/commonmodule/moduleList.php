<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('commonModuleImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('commonModuleImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getcommonModuleObj();
$obj->transfer($retObj);

?>

