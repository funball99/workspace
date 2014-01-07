<?php
abstract class abstractSaveWebRegiste {
	abstract function savewebregiste();
	function transfer($obj){
		echo echo_json($obj);
	}
}

?>