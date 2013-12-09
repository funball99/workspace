<?php
abstract class abstarctReplyList{
abstract function getReplyList();
	function transfer($array){
		echo echo_json($array);
	}
}

?>