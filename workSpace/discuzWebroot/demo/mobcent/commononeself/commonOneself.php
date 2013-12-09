<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('commonOneselfImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('commonOneselfImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getCommonOneselfObj();
$obj->transfer($retObj);
?>

