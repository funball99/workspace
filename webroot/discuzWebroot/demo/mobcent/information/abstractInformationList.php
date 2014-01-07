<?php
abstract class abstractInformationList {
	abstract function getInformationList();
	function transfer($array){
		echo echo_json($array);
	}
}

?>