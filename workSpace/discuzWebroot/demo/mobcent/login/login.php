<?php
//login
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('loginImpl').'.php';

$className = dynamicobject :: getShortDymanicObject('loginImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$obj = $classObj->login();
$classObj->transfer($obj); 
?>