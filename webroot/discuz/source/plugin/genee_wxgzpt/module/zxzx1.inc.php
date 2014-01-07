<?php
/**
 *      [G1 Studio^_^] (C)2012-2013.
 *
 *      $QQ:403172306
 *      $Id: 5473 2013-07-01 15:58 genee $
 */
if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}
global $_G;
$postlist = DB::fetch_all ( "SELECT subject,message,authorid,pid,author,dateline,position,tid FROM " . DB::table ( 'forum_post' ) . " WHERE invisible!=-5 AND first=1 ORDER BY dateline DESC LIMIT 10 " );

$myurl = 'http://' . $_SERVER ['SERVER_NAME'];
$setting ['attachurl'] = $myurl . '/data/attachment/';

$newsTpl = "";

$items = "";

foreach ( $postlist as $k => $v ) {
	
	$items .= "<item>";
	
	if (! isUTF8 ( $v ['subject'] )) {
		$v ['subject'] = diconv ( $v ['subject'], CHARSET, 'UTF-8' );
	}
	
	$items .= "<Title>" . "<![CDATA[" . $v ['subject'] . "]]>" . "</Title>";
	$items .= "<Description>" . "<![CDATA[]]>" . "</Description>";
	
	$pid = $v ['pid'];
	$isimage = 0;
	foreach ( C::t ( 'forum_attachment_n' )->fetch_all_by_id ( 'pid:' . $pid, 'pid', $pid ) as $attach ) {
		if ($attach ['isimage']) {
			$src = $setting ['attachurl'] . 'forum/' . $attach ['attachment'];
			$isimage = 1;
			$items .= "<PicUrl>" . "<![CDATA[" . $src . "]]>" . "</PicUrl>";
			break;
		
		}
	}
	
	if (! $isimage) {
		$items .= "<PicUrl>" . "<![CDATA[]]>" . "</PicUrl>";
	}
	
	$items .= "<Url>" . "<![CDATA[" . $myurl . "/forum.php?mod=viewthread&tid=" . $v [tid] . "]]>" . "</Url>";
	
	$items .= "</item>";
}

$newsTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[news]]></MsgType>
            <ArticleCount>%s</ArticleCount>
            <Articles>" . $items . "</Articles>
            <FuncFlag>0</FuncFlag>
            </xml>";
$resultStr = sprintf ( $newsTpl, $fromUsername, $toUsername, $time, count ( $postlist ) );
echo $resultStr;
?>