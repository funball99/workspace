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


/**
*	1.构造http请求包(包括签名)
*	2.发送http请求
*	3.解析响应包,返回一个对象,包含方法：
*		- getHttpCode()
*		- getErrorCode()
*		- getErrorMsg()
*		- getBody(): HTTP包体
*/

$baseDir = dirname(__FILE__) ;
require_once "$baseDir/common/Http.class.php";
require_once "$baseDir/common/Https.class.php";
require_once "$baseDir/common/Signature.class.php";
require_once "$baseDir/common/Conf.class.php";

class HttpRsp
{
    public $header = null;
	public $body = null;
	
	public function getHttpCode()	{	return $this->body['httpCode'];		}
	public function getErrorCode()	{	return $this->body['errorCode'];	}
	public function getErrorMsg()	{	return $this->body['errorMessage'];		}
	public function getErrorLink()	{	return $this->body['errorLink'];	}
    
    public function getHeader()		{	return $this->header;	}
	public function getBody()		{	return $this->body;     }
}

class ApiBase
{
   
	protected static function _GetSig($args)
	{
        return Signature::Get($args['secretId'],$args['secretKey'],$args['url'],$args['method'],$args['data']);
	}
    
	protected static function _SendByHttp($req)
	{
        $oHttp = new Http();
        return $oHttp->send($req);
	}
    
	protected static function _SendByHttps($req)
	{
        $oHttp = new Https();
        return $oHttp->send($req);
	}
	
    protected static function _GetRsp($rawRsp)
    {
        $rspObj = new HttpRsp();
        $httpArr = preg_split("/\n[\s| ]*\r/",$rawRsp);

        $rspObj->header = $httpArr[0];
        $rspObj->body = json_decode($httpArr[1],true);
        
        return $rspObj;
    }
	

}

