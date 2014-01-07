<?php
require_once '../../config/config_ucenter.php';
abstract class abstarctcommonModule{
	public function transfer($obj){
		 $res = echo_json($obj);
		 if(UC_DBCHARSET=='utf8'){
		 	$res = str_replace("\\u", "u", $res);
		 }
		 
		 
		 echo $res;
	}
	
	public abstract function getcommonModuleObj();
}

?>