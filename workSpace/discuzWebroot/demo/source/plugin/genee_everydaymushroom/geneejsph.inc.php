<?php
/**
 *      [G1 Studio] (C)2012-2013.
 *
 *      $Id: geneejs.inc.php 29558 2013-03-07 11:15 genee $
 */
if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}
if (empty ( $_SERVER ['HTTP_REFERER'] )) {
	exit ( 'Access Denied' );
}
global $_G;
require_once 'source/plugin/genee_everydaymushroom/include/genee.inc.php';

?>
 
function getPh(){
	var geneequest=geneeAjax();
	var url="plugin.php?id=genee_everydaymushroom:ajaxGetph&formhash=<?=FORMHASH?>";
	geneequest.open("POST", url, true);
	geneequest.send(null);
	geneequest.onreadystatechange=function() {
	    if (geneequest.readyState==4 && geneequest.status==200) {
	      	document.getElementById('ulph').innerHTML=geneequest.responseText;
			setTimeout("getPh()",<?=$gvar['ph_rftime']*1000;?>);  
	    }
	}
}	   

setTimeout("getPh()",<?=$gvar['ph_rftime']*1000;?>);  
