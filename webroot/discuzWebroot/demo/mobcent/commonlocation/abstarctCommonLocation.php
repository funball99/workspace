<?php
abstract class abstarctCommonLocation{
	public function transfer($obj){
		echo $res = echo_json($obj);
	}
	
	public abstract function getCommonLocationObj();
}

?>