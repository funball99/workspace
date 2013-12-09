<?php 
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('surroundTopicListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('surroundTopicListImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$surroundTopicList = $classObj->getSurroundTopicList();
$classObj->transfer($surroundTopicList);
?>