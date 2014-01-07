<?php
abstract class abstractUpdateUserInfo {
	abstract function updateUserInfo();
	function transfer($array){
		echo echo_json($array);
	}
}

?>