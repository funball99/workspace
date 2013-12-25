<?php
/**
 *      [G1 Studio!] (C)2012-2013.
 *
 *      QQ:403172306
 */

if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}

require_once 'source/plugin/genee_everydaymushroom/include/genee.inc.php';
$actives [$_G ['mod']] = "class='a'";

$alllj = DB::result_first ( "select sum(ljnum) from " . DB::table ( 'genee_mush_user' ) );

//count lv
$alllv = $gvar ['mushroomalv'] + $gvar ['mushroomblv'] + $gvar ['mushroomclv'] + $gvar ['mushroomdlv'] + $gvar ['mushroomelv'] + $gvar ['angellv'] + $gvar ['demonlv'] + $gvar ['visuallv'];

if ($_G ['genee_mod'] == 'mgdr') {
	
	$mgdr = DB::fetch_all ( "SELECT  ljnum, t.uid,t.username 
			FROM `" . DB::table ( 'genee_mush_user' ) . "` t  WHERE 1 and uid>0 order by t.ljnum desc
			LIMIT $gvar[mgdrnum]" );
	
	foreach ( $mgdr as $k => $v ) {
		$uids [] = $v [uid];
	}
	
	$mgph = DB::fetch_all ( "select sum(credit) credit,uid from " . DB::table ( 'genee_mush_ph' ) . " where uid in(" . implode ( ',', array_values ( $uids ) ) . ") group by uid" );
	
	foreach ( $mgph as $k => $v ) {
		$uidcredit [$v [uid]] = $v [credit];
	}
	
	foreach ( $mgdr as $k => $v ) {
		$mgdr [$k] ['credit'] = $uidcredit [$v ['uid']];
	}
	
	$i = 1;
	foreach ( $mgdr as $k => $v ) {
		$mgdr [$k] ['top'] = $i;
		$i ++;
	}

} elseif ($_G ['genee_mod'] == 'myjl') {
	
	if (empty ( $_G ['uid'] ))
		showmessage ( 'to_login', 'member.php?mod=logging&action=login', array (), array ('showmsg' => true, 'login' => 1 ) );
	
	$perpage = 20;
	$page = max ( 1, $_G ['genee_page'] );
	$start = ($page - 1) * $perpage;
	
	$myjl = DB::fetch_all ( "SELECT * FROM " . DB::table ( 'genee_mush_km' ) . " WHERE uid='$_G[uid]' ORDER BY create_time DESC limit $start,$perpage" );
	$count = DB::result_first ( "SELECT count(*) FROM " . DB::table ( 'genee_mush_km' ) . " WHERE uid='$_G[uid]' " );
	
	$multi = multi ( $count, $perpage, $page, "plugin.php?id=genee_everydaymushroom:genee_mushroom&mod=myjl" );

} elseif ($_G ['genee_mod'] == 'mgjs') {
	require_once libfile ( 'function/discuzcode' );
	
	$gvar ['mgjs'] = discuzcode ( $gvar ['mgjs'], 0, 0, $htmlon = 0, $allowsmilies = 1, $allowbbcode = 1, $allowimgcode = 1, $allowhtml = 1, $jammer = 0, $parsetype = '0', $authorid = '0', $allowmediacode = '1', $pid = 0, $lazyload = 0, $pdateline = 0, $first = 0 );

} else {
	
	$checkdb = "SELECT table_name FROM information_schema.TABLES WHERE TABLE_SCHEMA=%s and table_name=%s";
	$checkcopyright1 = DB::fetch_first ( $checkdb, array ($_G ['config'] ['db'] [1] ['dbname'], DB::table ( 'genee_everydaymushroom_config' ) ) );
	$iscopy = 0;
	if (! empty ( $checkcopyright1 )) {
		$mycopy = DB::result_first ( "SELECT zjdesc FROM %t WHERE zjname=%s", array ('genee_everydaymushroom_config', 'mycopy' ) );
		$iscopy = 1;
	}
	
	if ($_G ['genee_mod'] == 'post' && $_G ['uid'] && submitcheck('postsubmit')) {
		
		if ($_G ['genee_sendmessage'] == '') {
			showmessage ( $geneelang ['nrnonull'] );
		}
		
		$pid = DB::insert ( 'forum_post_tableid', array ('pid' => null ), 1 );
		$tid = $_G ['genee_tid'];
		$remoteip = getIp1 ();
		$fid = DB::result_first ( "select fid from " . DB::table ( 'forum_thread' ) . " where tid='$tid'" );
		
		DB::insert ( 'forum_post', array ('pid' => $pid, 'fid' => $fid, 'authorid' => $_G ['uid'], 'tid' => $tid, 'dateline' => TIMESTAMP, 'message' => $_G ['genee_sendmessage'], 'useip' => $remoteip, 'author' => $_G ['username'] ), 0 );
		DB::query ( "UPDATE " . DB::table ( 'forum_thread' ) . " SET views=views+1,replies=replies+1 WHERE tid='$tid'" );
		
		header ( "location:plugin.php?id=genee_everydaymushroom:genee_mushroom" );
	
	}
	
	$actives [mgdt] = "class='a'";
	$geneemoney = DB::fetch_first ( "SELECT * FROM " . DB::table ( "common_member_count" ) . " WHERE uid=$_G[uid]" );
	$credittype = $_G ['setting'] ['extcredits'] [$gvar ['exttype']] ['title'];
	
	$muser = DB::fetch_first ( "select * from " . DB::table ( 'genee_mush_user' ) . " where uid='$_G[uid]'" );
	if (empty ( $muser )) {
		$data ['uid'] = $_G ['uid'];
		$data ['username'] = $_G ['username'];
		$data ['playnum'] = $gvar ['daynum'];
		$muser ['playnum'] = $data ['playnum'];
		DB::insert ( 'genee_mush_user', $data );
	}
	
	$mushdt = DB::fetch_all ( "SELECT username,log,create_time FROM " . DB::table ( 'genee_mush_log' ) . "
	  order by create_time desc limit 10  " );
	
	$mushph = DB::fetch_all ( "SELECT username, sum( credit ) as credit , create_time
	FROM " . DB::table ( 'genee_mush_ph' ) . "
	where MONTH( FROM_UNIXTIME( create_time ) ) = MONTH( CURDATE( ) ) 
	AND WEEK( FROM_UNIXTIME( create_time ) ) = WEEK( CURDATE( ) ) 
	GROUP BY username
	ORDER BY credit DESC 
	LIMIT $gvar[ph_h]  " );
	
	$perpage = 20;
	$page = max ( 1, $_G ['genee_page'] );
	$start = ($page - 1) * $perpage;
	
	$postlist = DB::fetch_all ( "select subject,message,authorid,pid,author,dateline,position,tid from " . DB::table ( 'forum_post' ) . " where tid='$gvar[tid]' and invisible!=-5  order by position desc  limit $start,$perpage" );
	
	$count = DB::result_first ( "SELECT COUNT(*) FROM " . DB::table ( 'forum_post' ) . " WHERE  tid='$gvar[tid]' and invisible!=-5  " );
	
	require_once libfile ( 'function/discuzcode' );
	
	$setting ['attachurl'] = $_G ['setting'] ['attachurl'];
	
	foreach ( $postlist as $k => $v ) {
		$postlist [$k] ['dateline'] = dgmdate ( $postlist [$k] ['dateline'], 'Y-m-d H:i' );
		
		$message = $postlist [$k] ['message'];
		$message = discuzcode ( $message, 0, 0, $htmlon = 0, $allowsmilies = 1, $allowbbcode = 1, $allowimgcode = 1, $allowhtml = 1, $jammer = 0, $parsetype = '0', $authorid = '0', $allowmediacode = '1', $pid = 0, $lazyload = 0, $pdateline = 0, $first = 0 );
		
		$pid = $v ['pid'];
		foreach ( C::t ( 'forum_attachment_n' )->fetch_all_by_id ( 'pid:' . $pid, 'pid', $pid ) as $attach ) {
			if ($attach ['isimage']) {
				$src = $setting ['attachurl'] . 'forum/' . $attach ['attachment'];
				$message .= '<img src="' . $src . '" width="600px;"/>';
				$arr [] = array ('src' => $src, 'type' => 'img' );
			}
		}
		
		$message = preg_replace ( "/\[attach\](.+?)\[\/attach\]/is", "", $message );
		
		$postlist [$k] ['message'] = $message;
	}
	
	$multi = multi ( $count, $perpage, $page, "plugin.php?id=genee_everydaymushroom:genee_mushroom" );
	
	//rmgdr start
	$rmgdr = DB::fetch_all ( "SELECT username, sum( credit ) as credit , create_time,uid
	FROM " . DB::table ( 'genee_mush_ph' ) . "
	where date_format(FROM_UNIXTIME( create_time ),'%Y-%m-%d' ) =  CURDATE( )  
	GROUP BY username
	ORDER BY credit DESC 
	LIMIT 30  " );
	//rmgdr end
	

	//zmgdr start
	$zmgdr = DB::fetch_all ( "SELECT username, sum( credit ) as credit , create_time,uid
	FROM " . DB::table ( 'genee_mush_ph' ) . "
	where MONTH( FROM_UNIXTIME( create_time ) ) = MONTH( CURDATE( ) ) 
	AND WEEK( FROM_UNIXTIME( create_time ) ) = WEEK( CURDATE( ) ) 
	GROUP BY username
	ORDER BY credit DESC 
	LIMIT 30  " );
	//zmgdr end
	

	//ymgdr start
	$ymgdr = DB::fetch_all ( "SELECT username, sum( credit ) as credit , create_time,uid
	FROM " . DB::table ( 'genee_mush_ph' ) . "
	where MONTH( FROM_UNIXTIME( create_time ) ) = MONTH( CURDATE( ) ) 
	GROUP BY username
	ORDER BY credit DESC 
	LIMIT 30  " );
	//ymgdr end
	

	//ljmgdr start
	$ljmgdr = DB::fetch_all ( "SELECT username, sum( credit ) as credit , create_time,uid
	FROM " . DB::table ( 'genee_mush_ph' ) . "
	 
	GROUP BY username
	ORDER BY credit DESC 
	LIMIT 10  " );
	//ljmgdr end


}

include template ( 'genee_everydaymushroom:genee_mushroom' );

function getIp1() {
	if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
		$ip = getenv ( "HTTP_CLIENT_IP" );
	else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
		$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
	else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
		$ip = getenv ( "REMOTE_ADDR" );
	else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
		$ip = $_SERVER ['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return ($ip);
}

?>