<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('AnnouncementListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('AnnouncementListImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getAnnouncementListObj();
$obj->transfer($retObj);
?>

