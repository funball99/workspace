<?php
abstract class abstarctMessageHeart{
	public function transfer($obj){
		echo $res = echo_json($obj);
	}
	
	public abstract function getMessageHeartObj();
}

?>