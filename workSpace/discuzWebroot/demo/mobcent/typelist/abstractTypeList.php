<?php
abstract class abstractTypeList {
	abstract function getSubBoardList();
	function transfer($array){
		echo echo_json($array);
	}
}

?>