<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('FavoriteListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('FavoriteListImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getFavoriteListObj();
$obj->transfer($retObj);
?>