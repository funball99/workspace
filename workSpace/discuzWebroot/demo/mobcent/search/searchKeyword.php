<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('SearchKeywordImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('SearchKeywordImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$array = $classObj->getTopicList();
$classObj->transfer($array);

?>