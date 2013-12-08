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

//define your token
$geneetoken = $_G ['cache'] ['plugin'] ['genee_wxgzpt'] ['ac_token'];

define ( "TOKEN", $geneetoken );

class genee_wx {
	
	public function wx_valid() {
		$echoStr = $_GET ["echostr"];
		
		//valid signature , option
		if ($this->checkSignature ()) {
			echo $echoStr;
			exit ();
		}
	}
	
	public function wx_responseMsg() {
		global $_G;
		$gvar = $_G ['cache'] ['plugin'] ['genee_wxgzpt'];
		
		//get post data, May be due to the different environments
		$postStr = file_get_contents ( "php://input" );
		//extract post data
		if (! empty ( $postStr )) {
			$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
			
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$keyword = trim ( $postObj->Content );
			$time = time ();
			
			$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";
			
			$newsTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
	                    <CreateTime>%s</CreateTime>
	                    <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>1</ArticleCount>
                        <Articles>
                        <item>
                        <Title><![CDATA[%s]]></Title> 
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                        </item>                           
                        </Articles>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
			
			if ($postObj->Event == 'subscribe') {
				$msgType = "text";
				$contentStr = $gvar ['gz_ct'];
				$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				echo $resultStr;
				exit ();
			}
			
			if (! empty ( $keyword )) {
				
				$checkggk = DISCUZ_ROOT . "./source/plugin/genee_wxgzpt/module/ggk1.inc.php";
				if (file_exists ( $checkggk )) {
					require_once ($checkggk);
				}
				
				if (in_array ( $keyword, $gglfcodes )) {
					$checkggk = DISCUZ_ROOT . "./source/plugin/genee_wxgzpt/module/ggk2.inc.php";
					if (file_exists ( $checkggk )) {
						require_once ($checkggk);
					}
				
				} elseif ($keyword == $gvar ['ggl_f']) {
					$msgType = "text";
					$contentStr .= $gvar ['ggl_ct'] . "\n";
					foreach ( $hds as $k => $v ) {
						
						$contentStr .= $v ['fcode'] . "." . $v ['hd_name'] . "\n";
					}
					
					$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
					
					echo $resultStr;
				} elseif ($keyword == 'zxzx') {
					$checkzxzx = DISCUZ_ROOT . "./source/plugin/genee_wxgzpt/module/zxzx1.inc.php";
					if (file_exists ( $checkzxzx )) {
						require_once ($checkzxzx);
					}
 
				} else {
					$msgType = "text";
					$contentStr = $gvar ['function_ct'];
					$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
					
					echo $resultStr;
				}
			} else {
				echo "Input something...";
			}
		
		} else {
			echo "";
			exit ();
		}
	}
	
	private function checkSignature() {
		$signature = $_GET ["signature"];
		$timestamp = $_GET ["timestamp"];
		$nonce = $_GET ["nonce"];
		$token = TOKEN;
		
		$tmpArr = array ($token, $timestamp, $nonce );
		sort ( $tmpArr );
		$tmpStr = implode ( $tmpArr );
		$tmpStr = sha1 ( $tmpStr );
		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}
}
?>