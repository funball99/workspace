<?php
abstract class abstractLoginOut {
	abstract function getloginOutObj();
	function transfer($obj){
		echo echo_json($obj);
	}
}

?>