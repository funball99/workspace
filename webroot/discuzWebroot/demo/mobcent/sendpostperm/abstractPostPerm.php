<?php
abstract class abstractPostPerm {
	abstract function getPostPermObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>