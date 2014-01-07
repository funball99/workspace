<?php
abstract class abstractSendPost {
	abstract function getSendPostObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>