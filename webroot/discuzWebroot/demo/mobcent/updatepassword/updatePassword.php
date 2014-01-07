<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('updatePasswordImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('updatePasswordImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$password = $classObj->updatePassword();
$classObj->transfer($password);
?>