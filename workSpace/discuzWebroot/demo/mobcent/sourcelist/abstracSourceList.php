<?php
abstract class abstracSourceList {
	abstract function getSourceListImplObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>