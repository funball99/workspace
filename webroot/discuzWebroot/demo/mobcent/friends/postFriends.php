<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('postFriendsImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('postFriendsImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getFriendsObj();
$obj->transfer($retObj);
?>