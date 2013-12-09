<?php
abstract class abstractUpdataImg {
	abstract function updataImg();
	function transfer($obj){
		echo json_encode($obj);
	}
}

?>