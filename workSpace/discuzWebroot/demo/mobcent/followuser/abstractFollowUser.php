<?php
abstract class abstractFollowUser {
	abstract function getFollowUserObj();
	function transfer($array){
	    echo echo_json($array);	
	}
}

?>