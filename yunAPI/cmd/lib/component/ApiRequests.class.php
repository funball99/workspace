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
require_once "$baseDir/ApiBaseOpencloud.class.php";


class ApiRequests extends ApiBaseOpencloud
{
	
  	function __construct($host,$secretId,$secretKey)
  	{
  		ApiBaseOpencloud::setPara($host,$secretId,$secretKey);
  	}
	
    /**
	*	查询操作流水
    *  @args = array(
    *       'offset'        => int,
    *       'limit'         => int,
    *       'order'         => string,
    *       'begintime'     => string,
    *       'endtime'       => string,
    *       'method'        => string,
    *       'uri'           => string,
    *   );
    */
    public function GetRequests($args) 
    {
        $apiData = array(
            'url'=>'/v1/requests',
            'method'=>'GET',
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
        
		$rspObj = self::Send($apiData);
		
        return $rspObj;
    }
      
    /**
	*	查询任务详细信息
    */	
    public function GetRequest($instanceId = 0)
    {
        $apiData = array(
            'url'=>"/v1/requests/$instanceId",
            'method'=>'GET',
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData);
		
        return $rspObj;
    }
    
    /**
	*	查询操作流水
    *  @args = array(
    *       'offset'        => int,
    *       'begintime'     => string,
    *       'endtime'       => string,
    *       'uri'           => string,
    *   );
    */
    public function GetRequestList($args)
    {
    	$para = array(
    		'offset'        => isset($args['offset'])? (int)$args['offset'] : 0,
            'limit'         => 30,
    	);
    	if(isset($args['begintime'])){
    		$para['begintime'] = $args['begintime'];
    	}
    	if(isset($args['endtime'])){
    		$para['endtime'] = $args['endtime'];
    	}
    	if(isset($args['uri'])){
    		$para['uri'] = $args['uri'];
    	}
    	$requestInfo = self::GetRequests($para);
    	$requestList = $requestInfo->getbody();    	
    	if(200 == $requestList['httpCode'])
   		{
   			$body = $requestList;
   			$body['instanceInfo'][] = array('requestId','url','method','result','time');
   			if($requestList['num'] > 0)
   			{		
   				foreach($requestList['request'] as $key=>$val)
   				{
   					$body['instanceInfo'][] = array(
   						$val['requestId'],
   						$val['url'],
   						$val['method'],
   						$val['result'],
   						$val['reqTime'],
   					);
   				}
   			}
   			$requestInfo->body = $body;
   		}
   		return $requestInfo;
    }
    
}









