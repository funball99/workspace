<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('userTopicListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('userTopicListImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$userTopicList = $classObj->getUserTopicList();
$classObj->transfer($userTopicList);
?>