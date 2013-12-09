<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('replyImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('replyImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getReplyObj();
$obj->transfer($retObj);
?>