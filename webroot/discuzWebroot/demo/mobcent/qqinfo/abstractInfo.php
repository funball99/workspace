<?php
abstract class abstractInfo {
	abstract function getInfoObj();
	function transfer($array){
		echo echo_json($array);
	}
}
