<?php
abstract class abstractMsgList {
	abstract function getMsgListObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>