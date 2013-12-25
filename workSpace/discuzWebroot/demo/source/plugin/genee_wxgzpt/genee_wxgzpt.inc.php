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

require_once DISCUZ_ROOT . './source/plugin/genee_wxgzpt/include/genee.inc.php';
require_once DISCUZ_ROOT . './source/plugin/genee_wxgzpt/geneewxapi.class.php';

$wx = new genee_wx ( );

if ($gvar ['enable_auth']) {
	$wx->wx_valid ();
}

$wx->wx_responseMsg ();

?>