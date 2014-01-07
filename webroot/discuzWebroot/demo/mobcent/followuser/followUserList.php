<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('followUserImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('followUserImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$followUserList = $classObj->getFollowUserObj();
$classObj->transfer($followUserList);
?>