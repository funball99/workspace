<?php

/**
 *      [G1 Studio^_^] (C)2012-2013.
 *
 *      $QQ:403172306
 *      $Id: 5473 2013-07-01 15:58 genee $
 */
if (! defined ( 'IN_DISCUZ' ) || ! defined ( 'IN_ADMINCP' )) {
	exit ( 'Access Denied' );
}

require_once DISCUZ_ROOT . 'source/plugin/genee_wxgzpt/include/genee.inc.php';

$checkggk = DISCUZ_ROOT . "./source/plugin/genee_wxgzpt/module/ggk3.inc.php";

if (file_exists ( $checkggk )) {
	require_once ($checkggk);
} else {
	echo $geneelang ['pleaseazzj'];
}

?>
 