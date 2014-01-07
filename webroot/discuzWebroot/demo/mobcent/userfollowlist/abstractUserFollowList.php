<?php
abstract class abstractUserFollowList {
	abstract function getUserFollowList();
	function transfer($array){
		echo echo_json($array);
	}
}

?>