<?php
/**
 *      [G1 Studio^_^] (C)2012-2013.
 *
 *      $QQ:403172306
 *      $Id: admin_adcoop.inc.php 5473 2013-07-01 15:58 genee $
 */
if (! defined ( 'IN_DISCUZ' ) || ! defined ( 'IN_ADMINCP' )) {
	exit ( 'Access Denied' );
}
require_once DISCUZ_ROOT . './source/plugin/genee_wxgzpt/include/genee.inc.php';

if ($_G ['genee_mod'] == 'az') {
	
	if ($_G ['genee_cz'] == 'ggk') {
		$checkbuffermodule1 = 'source/plugin/genee_wxgzpt/module/ggk4.inc.php';
		if (file_exists ( $checkbuffermodule1 )) {
			require_once ($checkbuffermodule1);
		}
	
	}
	header ( "location:" . ADMINSCRIPT . "?action=plugins&operation=config&do=$pluginid&identifier=genee_wxgzpt&pmod=admin_module$extra" );

} elseif ($_G ['genee_mod'] == 'xz') {
	if ($_G ['genee_cz'] == 'ggk') {
		
		$checkbuffermodule2 = 'source/plugin/genee_wxgzpt/module/ggk5.inc.php';
		if (file_exists ( $checkbuffermodule2 )) {
			require_once ($checkbuffermodule2);
		}
	}
	
	header ( "location:" . ADMINSCRIPT . "?action=plugins&operation=config&do=$pluginid&identifier=genee_wxgzpt&pmod=admin_module$extra" );

} else {
	$adminscript = ADMINSCRIPT;
	
	$js = <<<EOF
    <script type="text/javascript">
    	function czModule(cztype,type){
    		if(type=="az"){
	    		if(confirm('$geneelang[confirmaz]')){
	    			location.href='$adminscript?action=plugins&operation=config&do=$pluginid&identifier=genee_wxgzpt&pmod=admin_module&mod=az&cz='+cztype;
	    		}
	    	}else{
				if(confirm('$geneelang[confirmxz]')){
	    			location.href='$adminscript?action=plugins&operation=config&do=$pluginid&identifier=genee_wxgzpt&pmod=admin_module&mod=xz&cz='+cztype;
	    		}
	    	}
    	}
    </script>
EOF;
	
	echo $js;
	showtableheader ( $geneelang ['wdzj'] );
	showtablerow ( '', '', array ('<b>' . $geneelang ['zjname'] . '</b>', '<b>' . $geneelang ['cz'] . '</b>', '<b>' . $geneelang ['status'] . '</b>' ) );
	
	//ggk module start
	$checkdb = "SELECT table_name FROM information_schema.TABLES WHERE TABLE_SCHEMA=%s and table_name=%s";
	$ggk = DB::fetch_first ( $checkdb, array ($_G ['config'] ['db'] [1] ['dbname'], DB::table ( 'genee_wxgz_hd' ) ) );
	if (! empty ( $ggk )) {
		$status = $geneelang ['yaz'];
		$cz = '<a href="javascript:;" onclick="czModule(\'ggk\',\'xz\')">' . $geneelang ['xz'] . '</a>';
	} else {
		
		$checkgglmodule1 = 'source/plugin/genee_wxgzpt/module/ggk1.inc.php';
		if (file_exists ( $checkgglmodule1 )) {
			$status = $geneelang ['waz'];
			$cz = '<a href="javascript:;" onclick="czModule(\'ggk\',\'az\')">' . $geneelang ['az'] . '</a>';
		} else {
			$status = $geneelang ['waz'];
			$cz = $geneelang ['download'];
		}
	}
	
	showtablerow ( '', '', array ($geneelang ['ggk'], $cz, $status ) );
	//ggk module end
	

	//zxzx module start
	$checkzxzxmodule1 = 'source/plugin/genee_wxgzpt/module/zxzx1.inc.php';
	if (file_exists ( $checkzxzxmodule1 )) {
		$status = $geneelang ['yaz'];
		$cz = $geneelang ['yaz'];
	} else {
		$status = $geneelang ['waz'];
		$cz = $geneelang ['download'];
	}
	showtablerow ( '', '', array ($geneelang ['zxzx'], $cz, $status ) );
	
	//zxzx module end
	

	showtablefooter ();
}
?>