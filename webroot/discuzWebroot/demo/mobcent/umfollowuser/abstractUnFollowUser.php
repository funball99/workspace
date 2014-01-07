<?php
abstract class abstractUnFollowUser {
	abstract function getunFollowUserObj();
	function transfer($array){
	    echo echo_json($array);	
	}
}

?>