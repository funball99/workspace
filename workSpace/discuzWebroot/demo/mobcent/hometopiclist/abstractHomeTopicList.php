<?php
abstract class abstractHomeTopicList {
	abstract function getHomeTopicList();
	function transfer($array){
		echo echo_json($array);
	}
}

?>