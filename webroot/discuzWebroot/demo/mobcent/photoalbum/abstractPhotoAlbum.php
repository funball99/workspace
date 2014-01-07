<?php
abstract class abstractPhotoAlbum {
	abstract function getPlugSignObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>