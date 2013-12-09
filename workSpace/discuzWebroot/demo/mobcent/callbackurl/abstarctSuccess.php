<?php
abstract class abstarctSuccess{
	public function transfer($obj){
		return $res = echo_json($obj);
	}
	
	public abstract function getSuccessObj($token,$userArr);
}

?>