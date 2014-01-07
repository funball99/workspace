<?php
abstract class abstractpostsNotices {
	abstract function getpostsNoticesObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>