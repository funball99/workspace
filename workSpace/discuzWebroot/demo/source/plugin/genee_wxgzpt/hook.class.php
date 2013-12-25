<?php
/**
 *      [G1 Studio!] (C)2012-2013.
 *
 *      $ID: 5473 genee 
 *      $QQ: 403172306
 */
if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}
 

class plugin_genee_wxgzpt {
	function global_nav_extra() {
		global $_G;
		$geneelang = lang ( 'plugin/genee_wxgzpt' );
		
		$wxnav = '<ul><li id="mn_geneewx"><a href="javascript:;" onclick="showWindow(\'genee_wxgzpt\', \'plugin.php?id=genee_wxgzpt:genee_gzh\',\'get\',0);return false;" title="'.$geneelang[wxgzh].'">'.$geneelang[wxgzh].'</a></li></ul>';
		
		return $wxnav;
	}
	
}
			
			
			

?>