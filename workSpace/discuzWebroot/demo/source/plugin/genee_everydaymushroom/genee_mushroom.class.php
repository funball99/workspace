<?php

if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}

class plugin_genee_everydaymushroom {
	
	function global_usernav_extra1($content) {
		global $_G;
		require 'source/plugin/genee_everydaymushroom/include/genee.inc.php';
		
		if ($_G [uid] && $gvar ['ifkzct']) {
			$playnum = DB::result_first ( "select playnum from " . DB::table ( 'genee_mush_user' ) . " where uid='$_G[uid]'" );
			$gvar ['kzcomment'] = str_replace ( '{$mgnum}', $playnum, $gvar ['kzcomment'] );
			
			$content = '<a href="plugin.php?id=genee_everydaymushroom:genee_mushroom"><span  style="color:red;margin-left:2px;font-weight:bold;">' . $gvar ['kzcomment'] . '</span></a>';
		}
		return $content;
	}
}

class plugin_genee_everydaymushroom_forum extends plugin_genee_everydaymushroom {
	
	function post_genee_everydaymushroom_message($params) {
		global $_G, $tid, $pid, $threadimageaid, $message, $modthread, $htmlon;
		list ( $msg, $url_forward, $values, $extraparam ) = $params ['param'];
		
		if (in_array ( $msg, array ('post_reply_succeed' ) ) && $_GET ['handlekey'] == 'gmush_reply') {
			showmessage ( 'ok', null, array (), array ('showdialog' => 0, 'showmsg' => false, 'closetime' => 0.0001,
			 'extrajs' => '<script type="text/javascript">gmush_location();</script>' ) );
		}
	}
}
?>