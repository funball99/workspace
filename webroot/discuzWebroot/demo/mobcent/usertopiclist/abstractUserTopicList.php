<?php
abstract class abstractUserTopicList {
	abstract function getUserTopicList();
	function transfer($array){
		echo echo_json($array);
	}
}

?>