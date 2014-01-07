<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('postReportUserImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('postReportUserImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getReportUserObj();
$obj->transfer($retObj);
?>