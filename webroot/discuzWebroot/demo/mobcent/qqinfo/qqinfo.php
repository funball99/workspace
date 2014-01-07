<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('qqInfoImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('qqInfoImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getInfoObj();
$obj->transfer($retObj);