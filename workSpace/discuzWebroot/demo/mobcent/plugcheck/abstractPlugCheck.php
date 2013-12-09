<?php
abstract class abstractPlugCheck {
	abstract function getPlugSignObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>