<?php
/**
 *      [G1 Studio] (C)2012-2013.
 *
 *      $Id: geneejs.inc.php 29558 2013-03-07 11:15 genee $
 */
if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}
 
global $_G;
require_once 'source/plugin/genee_everydaymushroom/include/genee.inc.php';

?>

var scrollDelay=10;
var Scroll=document.getElementById("scroll"); 
var Scroll2=document.getElementById("scroll2"); 
var currentTop=0,preTop=0,stoptime=0,stopscroll=false; 
var ScrollChild=Scroll.getElementsByTagName("li"); 
var ScrollHeight=Scroll.offsetHeight; 
function ScrollInfo(){ 

    if(stopscroll==true) return; 
    currentTop++; 
    if(currentTop+1>=ScrollHeight){ 
        currentTop--; 
        stoptime++; 
	    if(stoptime==parseInt(ScrollHeight)*scrollDelay) { 
			currentTop=0; 
			stoptime=0; 
		} 
    }else{ 
        preTop=Scroll.scrollTop; 
        Scroll.scrollTop++; 
        if(preTop==Scroll.scrollTop){ 
			 Scroll.scrollTop=Scroll2.offsetHeight-ScrollHeight; 
			 Scroll.scrollTop+=1; 
 		} 
    } 
} 
function Int_Scroll(){ 
    Scroll2.innerHTML=""; 
    Scroll2.innerHTML=Scroll.innerHTML; 
    Scroll.innerHTML=Scroll2.innerHTML+Scroll2.innerHTML; 
    Scroll.onmouseover=function(){ 
        stopscroll=true; 
    } 
    Scroll.onmouseout=function(){ 
        stopscroll=false; 
    } 
    setInterval("ScrollInfo()",scrollDelay); 
} 
window.setTimeout("Int_Scroll()",1000);  


function getDt(){
	var geneequest=geneeAjax();
	var url="plugin.php?id=genee_everydaymushroom:ajaxGetdt&formhash=<?=FORMHASH?>";
	geneequest.open("POST", url, true);
	geneequest.send(null);
	geneequest.onreadystatechange=function() {
	    if (geneequest.readyState==4 && geneequest.status==200) {
	      	document.getElementById('scroll').innerHTML=geneequest.responseText;
			setTimeout("getDt()",<?=$gvar['dt_rftime']*1000;?>);  
	    }
	}
}	   

setTimeout("getDt()",<?=$gvar['dt_rftime']*1000;?>);  
