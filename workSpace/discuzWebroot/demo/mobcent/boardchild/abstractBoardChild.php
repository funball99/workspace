<?php
abstract class abstractBoardChild {
	abstract function getSubBoardList();
	function transfer($array){
		echo echo_json($array);
	}
}

?>