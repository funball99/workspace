<?php
abstract class abstarctAnnouncement{
	public function transfer($obj){
		echo echo_json($obj);
	}
	
	public abstract function getAnnouncementListObj();
}

?>