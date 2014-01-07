<?php
abstract class abstractPostUpdataImg {
	abstract function getPostUpdataImgObj();
	function transfer($obj){
		echo json_encode($obj);
	}
}

?>