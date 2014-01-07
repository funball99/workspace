<?php
abstract class abstractReplyPerm {
	abstract function getReplyPermObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>