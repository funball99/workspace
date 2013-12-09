<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('replyPermImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('replyPermImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getReplyPermObj();
$obj->transfer($retObj);
?>