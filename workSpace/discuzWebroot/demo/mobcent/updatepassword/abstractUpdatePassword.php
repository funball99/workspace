<?php
abstract class abstractUpdatePassword {
	abstract function updatePassword();
	function transfer($array){
		echo echo_json($array);
	}
}

?>