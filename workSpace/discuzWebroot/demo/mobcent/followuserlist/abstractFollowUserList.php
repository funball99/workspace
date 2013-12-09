<?php
abstract class abstractFollowUserList {
	abstract function getFollowUserList();
	function transfer($array){
	    echo echo_json($array);	
	}
}

?>