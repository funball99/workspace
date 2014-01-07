<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('msgReplyImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('msgReplyImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getmsgReplyObj();
$obj->transfer($retObj);
?>

