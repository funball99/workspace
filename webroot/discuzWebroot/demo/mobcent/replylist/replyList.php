<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('replyListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('replyListImpl');
$class=new ReflectionClass($className);

$obj = $class->newInstance();
$retObj = $obj->getReplyList(); 
$obj->transfer($retObj);

?>