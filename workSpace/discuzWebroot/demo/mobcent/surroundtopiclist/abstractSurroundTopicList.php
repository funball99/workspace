<?php
abstract class abstractSurroundTopicList {
	abstract function getSurroundTopicList();
	function transfer($array){
		echo echo_json($array);
	}
}

?>