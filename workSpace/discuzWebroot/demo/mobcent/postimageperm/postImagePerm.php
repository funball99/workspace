<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('postImagePermImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('postImagePermImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getpostImagePermImplObj();
$obj->transfer($retObj);
?>