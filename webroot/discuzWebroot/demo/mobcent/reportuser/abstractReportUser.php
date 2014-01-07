<?php
abstract class abstractReportUser {
	abstract function getReportUserObj();
	function transfer($array){
		echo json_encode($array);
	}
}

?>