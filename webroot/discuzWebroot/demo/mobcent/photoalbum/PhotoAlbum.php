<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('PhotoAlbumImpl').'.php'; 
$className = dynamicobject :: getShortDymanicObject('PhotoAlbumImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getPlugSignObj();
$obj->transfer($retObj);
?>