<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('updateUserInfoImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('updateUserInfoImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$userInfo = $classObj-> updateUserInfo();
$classObj->transfer($userInfo);
?>