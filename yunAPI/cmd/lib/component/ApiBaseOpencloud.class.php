<?php

# Copyright 2013 Tecent Inc.
# All rights reserved.
#
# Permission is hereby granted, free of charge, to any person obtaining a
# copy of this software and associated documentation files (the
# "Software"), to deal in the Software without restriction, including
# without limitation the rights to use, copy, modify, merge, publish, dis-
# tribute, sublicense, and/or sell copies of the Software, and to permit
# persons to whom the Software is furnished to do so, subject to the fol-
# lowing conditions:
#
# The above copyright notice and this permission notice shall be included
# in all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
# OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABIL-
# ITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT
# SHALL THE AUTHOR BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
# WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
# IN THE SOFTWARE.
#



$baseDir = dirname(__FILE__) ;
require_once "$baseDir/../ApiBase.class.php";


class ApiBaseOpencloud extends ApiBase
{
	private static $_endPoint = null;
	private static $_secretId = null;
	private static $_secretKey = null;
	
	protected static function setPara($host,$secretId,$secretKey)
	{
		self::$_endPoint = $host;
		self::$_secretId = $secretId;
		self::$_secretKey = $secretKey;		
	}
	
	public static function Send($apiData = array(),$IsHttps = false)
	{
		$conf = self::_GetConf();
        
        $req['url']         = $apiData['url'];
        $req['method']      = $apiData['method'];
        $req['data']        = is_array($apiData['body']) ? json_encode($apiData['body']) : $apiData['body'];
        $req['secretId']    = self::$_secretId;
        $req['secretKey']   = self::$_secretKey;
        $req['timeout']     = $conf['timeout'];
        
        $host = self::$_endPoint;
		
        $arr = self::_GetSig($req);
		$req['header'] = $arr['header'];
        
		if($IsHttps) {
			$req['url'] = "https://".$host.$apiData['url'];
			$rawRsp = self::_SendByHttps($req);
		} else {
			$req['url'] = "http://".$host.$apiData['url'];
			$rawRsp = self::_SendByHttp($req);
		}
		return self::_GetRsp($rawRsp);
	}

	private static function _GetConf()
	{
        $baseDir = dirname(__FILE__) ;
		$commonConf = Conf::LoadFile("$baseDir/../common.cfg");
        
		return array(
			'timeout'   =>$commonConf['common']['timeout'],
		);
	}	
	

}