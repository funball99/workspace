<?php
abstract class abstractWebLogin {
	abstract function login();
	function transfer($obj){
		echo echo_json($obj);
	}
}

?>