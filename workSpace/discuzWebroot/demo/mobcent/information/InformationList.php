<?php 

require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('InformationImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('InformationImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$homeTopicList = $classObj->getInformationList();
$classObj->transfer($homeTopicList);
?>