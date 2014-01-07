<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('postsNoticesImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('postsNoticesImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getpostsNoticesObj();
$obj->transfer($retObj);
?>