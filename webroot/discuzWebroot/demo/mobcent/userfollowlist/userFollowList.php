<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('userFollowListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('userFollowListImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$userFollowList = $classObj->getUserFollowList();
$classObj->transfer($userFollowList);
?>