<?php
abstract class abstractLogin {
	abstract function login();
	function transfer($obj){
		echo echo_json($obj);
	}
}

?>