<?php
abstract class abstractRecommendUsersList {
	abstract function getRecommendUsers();
	function transfer($array){
		echo echo_json($array);
	}
}

?>