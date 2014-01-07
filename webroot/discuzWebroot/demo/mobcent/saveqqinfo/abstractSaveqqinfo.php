<?php
abstract class abstractSaveqqinfo {
	abstract function saveqqInfo();
	function transfer($obj){
		echo echo_json($obj);
	}
}