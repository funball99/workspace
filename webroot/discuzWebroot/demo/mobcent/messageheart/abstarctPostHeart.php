<?php
abstract class abstarctPostHeart{
	public function transfer($obj){
		echo $res = echo_json($obj);
	}
	
	public abstract function getPostHeartObj();
}

?>