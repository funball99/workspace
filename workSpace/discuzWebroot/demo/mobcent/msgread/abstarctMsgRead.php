<?php
abstract class abstarctMsgRead{
	public function transfer($obj){
		echo $res = echo_json($obj);
	}
	
	public abstract function getMsgReadObj();
}

?>