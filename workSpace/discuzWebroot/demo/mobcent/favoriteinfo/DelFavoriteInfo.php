<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('FavoriteInfoImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('FavoriteInfoImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getDelFavoriteInfoObj();
$obj->transfer($retObj);
?>