<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('followUserListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('followUserListImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$followUserList = $classObj->getFollowUserList();
$classObj->transfer($followUserList);
?>