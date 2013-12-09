<?php
abstract class abstractPostImagePerm {
	abstract function getpostImagePermImplObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>