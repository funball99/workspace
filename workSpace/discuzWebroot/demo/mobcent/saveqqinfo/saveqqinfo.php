<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('saveqqinfoImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('saveqqinfoImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$res = $classObj->saveqqInfo();
$classObj->transfer($res);