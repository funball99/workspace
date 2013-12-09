<?php
try{
	require_once '../Config/dynamicobject.php';
	require_once './'.dynamicobject :: getShortDymanicObject('recommendUsersListImpl').'.php';
	$className = dynamicobject :: getShortDymanicObject('recommendUsersListImpl');
	$class=new ReflectionClass($className);
	$classObj = $class->newInstance();
	$recommendUsers = $classObj->getRecommendUsers();
	$classObj->transfer($recommendUsers);
}catch(Exception $e){
			file_put_contents("./error.log", $e->getMessage());
	}

?>