<?php
abstract class abstractReply {
	abstract function getReplyObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>