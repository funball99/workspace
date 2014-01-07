<?php
abstract class abstractCommonPictureSet {
	abstract function getCommonPictureSetObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>