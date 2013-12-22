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
require_once("$baseDir/ApiBaseOpencloud.class.php");


class ApiCvms extends ApiBaseOpencloud
{
  	function __construct($host,$secretId,$secretKey)
  	{
  		ApiBaseOpencloud::setPara($host,$secretId,$secretKey);
  	}
      
	/**
	*	查询cvm实例列表
    *	@args = array(
    *       'lanips'   		=> string,
    *       'offset'        => int,
    *       'limit'         => int,
    *   );
    */
    public function GetCvms($args = array(),$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/cvms",
            'method'=>"GET",
            'body'=>NULL,
        );
        
    	if(!empty($args)) {
            $apiData['url'] .= "?";
            foreach($args as $k => $v) {
            
                //kick off useless options
                if(is_string($v) && empty($v))  continue;
                
                $apiData['url'] .= "&$k=$v";
            }
        }
        
		$rspObj = ApiBaseOpencloud::Send($apiData,$Https);
		
        return $rspObj;
    }
    
    /**
    * 	cmd查询cvm实例列表
    * 	@args = array(
    * 		'ip'=> string, 支持批量，用逗号分割
    * 	)
    */	
    public function GetCvmsRet($args = array() )
    {
    	$statusArr = array(
    		0 =>'creating',
    		1 =>'normal',
    		2 =>'fail',
    	);
    	$tempArgs = array();
    	if( isset($args['ip']) )
    	{
    		$tempArgs['lanips'] = $args['ip'];
    	}
    	
    	$getCvmObj = self::GetCvms($tempArgs);
    	$getCvmBody = $getCvmObj->getbody();
    	$getCvmBody['instanceInfo'] = array();
    	if(200 == $getCvmBody['httpCode'])
    	{
    		$getCvmBody['instanceInfo'][] = array('lanIp','wanIp','cpu','mem','disk','bandwidth','os','status');
    		if(count($getCvmBody['instances']) > 0)
    		{
    			foreach($getCvmBody['instances'] as $key =>$val)
    			{
    				$val = $val['instanceInfo'];
    				$status = $statusArr[ $val['status'] ] ? $statusArr[ $val['status'] ] : "normal";
    				$getCvmBody['instanceInfo'][] = array(
    					$val['lanIp'],
    					$val['wanIp'],
    					$val['cpu'],
    					$val['mem'],
    					$val['disk'],
    					$val['bandwidth'],
    					$val['os'],
    					$status,
    				);
    			}
    		}
    		$getCvmObj->body = $getCvmBody;
    	}

    	return $getCvmObj;
    }
    
	/**
	*	查询cvm绑定域名
	*	@args : string,查询CVM的lanip
    */	
    public function GetCvmDomains($args = '',$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/cvms/domains/$args",
            'method'=>'GET',
            'body'=>NULL,
        );
		$rspObj = ApiBaseOpencloud::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	查询cvm登录Token
    */	
    public function GetCvmToken($Https = false)
    {
        $apiData = array(
            'url'=>"/v1/cvms/token",
            'method'=>"GET",
            'body'=>NULL,
        );
		$rspObj = ApiBaseOpencloud::Send($apiData,$Https);
        
		return $rspObj;
    }
    
    /**
	*	cmd查询cvm登录Token
    */	
    public function GetCvmTokenQc()
    {
    	$rspObj = self::GetCvmToken();
    	$retBody = $rspObj->getBody();
		$retBody['instanceInfo'] = array();
		if(200 == $retBody['httpCode'])
		{
			$retBody['instanceInfo'][] = array('token');
			$retBody['instanceInfo'][] = array($retBody['token']);
		}
		$rspObj->body = $retBody;
		return $rspObj;
    }
    
}