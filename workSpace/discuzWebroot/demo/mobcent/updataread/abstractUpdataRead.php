<?php
abstract class abstractUpdataRead {
	abstract function getUpdataReadObj();
	function transfer($obj){
		echo echo_json($obj);
	}
}

?>