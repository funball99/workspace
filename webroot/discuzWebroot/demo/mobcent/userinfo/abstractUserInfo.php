<?php
abstract class abstractUserInfo {
	abstract function getUserInfoObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>