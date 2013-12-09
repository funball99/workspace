<?php
abstract class abstarctBoardList{
	public function transfer($obj){

		echo $res = echo_json($obj);
	}
	
	public abstract function getBoardListObj();
}

?>