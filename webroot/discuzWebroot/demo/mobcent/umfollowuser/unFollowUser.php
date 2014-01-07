<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('unFollowUserImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('unFollowUserImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$followUserList = $classObj->getunFollowUserObj();
$classObj->transfer($followUserList);
?>