<?php

//cronname:genee_everydaymushroom
//week:
//day:
//hour:1
//minute:0


if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}

global $_G;
loadcache ( 'plugin' );

$gvar = $_G ['cache'] ['plugin'] ['genee_everydaymushroom'];

$data ['playnum'] = $gvar ['daynum'];
DB::update ( 'genee_mush_user', $data, "1=1" );

?>