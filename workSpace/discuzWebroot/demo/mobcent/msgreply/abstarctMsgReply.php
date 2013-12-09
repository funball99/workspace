<?php
abstract class abstarctMsgReply{
	public function transfer($obj){
		echo $res = echo_json($obj);
	}
	public abstract function getmsgReplyObj();
}

?>