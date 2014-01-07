<?php
/**
 *      [G1 Studio^_^] (C)2012-2013.
 *
 *      $Id: geneejs.inc.php 29558 2013-03-07 11:15 genee $
 */
if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}
global $_G;
$gvar = $_G ['cache'] ['plugin'] ['genee_everydaymushroom'];
$geneelang = lang ( 'plugin/genee_everydaymushroom' );
?>

 
function geneeAjax(){
   try {
		geneequest = new XMLHttpRequest();
	} catch (m1) {
		try {
			geneequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (m2) {
			try {
				geneequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (failed) {
				geneequest = false;
			}
		}
	} 
	
  return geneequest;	
}


function getMushRoom() {
	var geneequest=geneeAjax();
	if(<?=$_G ['uid']?>){
		url="plugin.php?id=genee_everydaymushroom:ajaxHandle&formhash=<?=FORMHASH?>";
		
		jqMush('#getmushroom')[0].innerHTML='<img src="' + IMGDIR + '/loading.gif"> '+'<?=$geneelang['sscjz']?>';
		jqMush('#imgmush')[0].innerHTML='<img src="<?=htmlspecialchars($gvar['run']);?>"/>';
		
		jqMush('.btn_cf').attr("disabled","disabled");
		
		var gb64 = new GeneeBase64();
		geneequest.open("POST", url, true);
		geneequest.send(null);
		geneequest.onreadystatechange=function() {
	        if (geneequest.readyState==4 && geneequest.status==200) {
	        jqMush('.btn_cf').removeAttr("disabled");
	        <?php if ($_G['charset']=='utf-8'){?>
	        	var data=gb64.decode(geneequest.responseText);
	        <?php }else{?>
	        	var data=strAnsi2Unicode(decode64(geneequest.responseText));
	        <?php }?>
	         	var geneearr=data.split("&&&&");
		        if(geneearr[1]=='nocredit'){
		         	jqMush('#getmushroom')[0].innerHTML='<?=$geneelang['qadyh']?>';
		         	showDialog(geneearr[0], 'notice',  '<img src="' + IMGDIR + '/loading.gif"> ', null, true, null, '', '', '', 5);
		        }else if(geneearr[1]=='noplaynum'||geneearr[1]=='close'){
		         	jqMush('#getmushroom')[0].innerHTML=geneearr[0];
		         	showDialog(geneearr[0], 'notice',  '<img src="' + IMGDIR + '/loading.gif"> ', null, true, null, '', '', '', 5);
		        }else{
		        	 jqMush('#getmushroom')[0].innerHTML=geneearr[0];
		         	 jqMush('#imgmush')[0].innerHTML='<img src="'+geneearr[2]+'"/>';
		      		 jqMush('#geneemoney')[0].innerHTML=parseInt(parseInt(jqMush('#geneemoney')[0].innerHTML)+parseInt(geneearr[1]));
		         	 jqMush('#playnum')[0].innerHTML=parseInt(parseInt(jqMush('#playnum')[0].innerHTML)-parseInt(1));
		         	
		        }
	          	showCreditPrompt();
	          	 
          	 }
          	
        }
     }
 }   
 
