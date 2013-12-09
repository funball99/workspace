<?php
abstract class abstractFavoriteInfo {
	abstract function getAddFavoriteInfoObj();
	abstract function getDelFavoriteInfoObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>