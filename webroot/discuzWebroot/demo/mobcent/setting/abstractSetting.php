<?php
abstract class abstractSetting {
	abstract function getPlugSignObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>