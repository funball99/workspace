<?php 

require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('homeTopicListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('homeTopicListImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$homeTopicList = $classObj->getHomeTopicList();
$classObj->transfer($homeTopicList);
?>