<?php

class plugin_saya_mails{
	function global_footer() {
		global $_G;
if($_G[member][newpm] or $_G[member][newprompt]){
		$uid = $_G['uid'];
		if($uid != ''){
		$sayavoiceclass = DB::fetch_first("SELECT * FROM ".DB::table('common_member')." WHERE uid=".$uid."");
	$hitv=$sayavoiceclass[saya_hitvoice];
	$pmv=$sayavoiceclass[saya_pmvoice];
	$sayapmset=DB::fetch_first("SELECT * FROM ".DB::table('saya_mails')." WHERE musicid=".$pmv."");
	$sayahitset=DB::fetch_first("SELECT * FROM ".DB::table('saya_mails')." WHERE musicid=".$hitv."");

		  	if($_G[member][newprompt]){
			  $saya_prompt ='<embed src="source/plugin/saya_mails/mail/'.$sayahitset[dir].'" controls=playbutton  volume="100" autostart=true hidden="true" loop="false">';
			  }
		  if($_G[member][newpm]){
			  $saya_pm ='<embed src="source/plugin/saya_mails/mail/'.$sayapmset[dir].'" controls=playbutton  volume="100" autostart=true hidden="true" loop="false">';
			  }
		}

}
	

	
return '
'.$saya_prompt.'
'.$saya_pm.'
';
}
	
}
?>