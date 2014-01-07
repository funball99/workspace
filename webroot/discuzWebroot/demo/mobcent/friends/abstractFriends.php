<?php
abstract class abstractFriends {
	abstract function getFriendsObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>