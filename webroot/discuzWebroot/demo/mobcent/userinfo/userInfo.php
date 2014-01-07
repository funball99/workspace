<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('userInfoImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('userInfoImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$userTopicList = $classObj->getUserInfoObj();
$classObj->transfer($userTopicList);
?>