<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('sourceListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('sourceListImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getSourceListImplObj();
$obj->transfer($retObj);
?>