<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('userReplyListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('userReplyListImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$userReplyList = $classObj->getUserReplyList();
$classObj->transfer($userReplyList);

?>