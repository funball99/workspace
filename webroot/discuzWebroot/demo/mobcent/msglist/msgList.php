<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('msgListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('msgListImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$subBoardInfo = $classObj->getMsgListObj();
$classObj->transfer($subBoardInfo);
?>

