<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('topicImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('topicImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getTopicObj();
$obj->transfer($retObj);
?>