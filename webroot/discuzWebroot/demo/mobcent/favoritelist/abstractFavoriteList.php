<?php
abstract class abstractFavoriteList {
	abstract function getFavoriteListObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>