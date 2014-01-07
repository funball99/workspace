<?php
abstract class abstractUserReplyList {
     abstract function getUserReplyList();
     function transfer($array){
     	echo echo_json($array);
     }
}

?>