<?php
abstract class abstractTopicList {
	abstract function getSubBoardList();
	abstract function getTopicList();
	function transfer($array){
		echo echo_json($array);
	}
}

?>