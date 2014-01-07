<?php
abstract class abstractSearchKeyword {
	abstract function getTopicList();
	function transfer($arr){
		echo echo_json($arr);
	}
}

?>