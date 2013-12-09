<?php
abstract class abstractRegiste {
	abstract function registe();
	function transfer($obj){
		echo echo_json($obj);
	}
}

?>