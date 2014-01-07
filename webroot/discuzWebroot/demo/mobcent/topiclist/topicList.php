<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('topicListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('topicListImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$subBoardInfo = $classObj->getSubBoardList();
$threadList = $classObj->getTopicList();
$array = array_merge($subBoardInfo,$threadList);
$classObj->transfer($array);
?>

