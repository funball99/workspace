<?php
abstract class abstractPlugSign {
	abstract function getPlugSignObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>