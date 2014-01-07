<?php
abstract class abstractTopic {
	abstract function getTopicObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>