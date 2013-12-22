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
$baseDir = dirname(__FILE__) ;
require_once "$baseDir/ApiRequests.class.php";


class ApiDomains extends ApiBaseOpencloud
{	
	const BIND_SUCCESS    = 200;
	const BIND_PROCESSING = 202;
	
  	function __construct($host,$secretId,$secretKey)
  	{
  		ApiBaseOpencloud::setPara($host,$secretId,$secretKey);
  	}
	
    /**
	*	根据域名查资源ID
	*	@args : string,查询的domains,逗号分割
    */
    public function GetIdbyDomains($args,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/domains/query_instance_id?domains=$args",
            'method'=>"GET",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
	
	
	/**
	*	查询域名列表
    *	@args = array(
    *		'instanceids'   =>string, 
    *       'type'   		=> int,
    *       'offset'        => int,
    *       'limit'         => int,
    *   );
    */
    public function GetDomains($args,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/domains",
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
                
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
    /**
	*	查询域名信息
    */
    public function GetDomainInstance($instanceId,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/domains/$instanceId",
            'method'=>"GET",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	创建域名
    *	@args = array(
    *       'domain'   => string,
    *   );
    */
    public function CreateDomain($args,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/domains",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
    /**
	*	删除域名
    */
    public function DeleteDomain($instanceId,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/domains/$instanceId",
            'method'=>"DELETE",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	域名绑定云服务器单端口
    *	@args = array(
    *       	'lanIps'   => array("ip1","ip2",...),
    *       	'port'     => int,
    *   );
    */
    public function DomainsBindCvmPort($instanceId,$args,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/domains/$instanceId/cvm_bind",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https);
			
        return $rspObj;
    }
    
	/**
	*	cmd域名绑定cvms
    *	@args = array(
    *			'domain'   => string,
    *       	'lanIps'   => "ip1","ip2",...,
    *       	'port'     => int,
    *   );
    */
    public function DomainBind($args,$Https = false)
    {
    	$getInsIdObj = self::GetIdbyDomains($args['domain']);
    	$getInsIdBody = $getInsIdObj->getbody();
    	if(200 == $getInsIdBody['httpCode'])
    	{
    		$instanceId = isset($getInsIdBody['instanceIds'][ $args['domain'] ])?$getInsIdBody['instanceIds'][ $args['domain'] ]:1;
    	}
    	else
    	{
    		return $getInsIdObj;
    	}
    	
    	$tempArgs = array(
    		'lanIps' => explode(',', $args['ip']),
    		'port'   => (int)$args['port'],
    	);
    	
    	$rspObj = self::DomainsBindCvmPort($instanceId,$tempArgs);	
        return $rspObj;
    } 
    
    /**
    *	cmd查询绑定结果 	
    * 	@args = array(
    * 		'requestId' => int,
    * 		'domain'    => string
    * 	)      
    */
    public function GetDomainBindRet($args)
    {
    	$reqId  = $args['requestId'];
    	$domain = $args['domain'];
    	$resultObj = ApiRequests::GetRequest($reqId);
        $retBody = $resultObj->getbody();
    	if(202 != $retBody['httpCode'])
    	{
    		$retBody['httpCode'] = '200';  
    		$errorCode = $retBody['errorCode'];
    		$errorMessage = $retBody['errorMessage'];
            $retBody['instanceInfo'][] = array('domain','ip','port','result','errorCode','errorMessage');
			$instanceId = $retBody['rsp']['instanceId'];
			$getDomain = self::GetDomainInstance($instanceId);
			$domainInfo = $getDomain->getBody();
            $domain = $domainInfo['instanceInfo']['domain'];
			
			$args = $retBody['req']['body']['lanIps'];
            $port = $retBody['req']['body']['port'];
			if(!empty($args))
			{
				foreach($args as $key => $value)
				{
					$ip = $value;
					$temArr = array(
                        'lanIp' =>$ip,
                        'port'  =>$port,
                    );
					if(!in_array($temArr,$domainInfo['instanceInfo']['devicesList']))
					{
						$result = "BIND FAIL";
					}
					else
					{
						$result = "BIND SUCC";
						$errorCode = "200";
						$errorMessage = "OK";
					}
					$retBody['instanceInfo'][] = array($domain,$ip,$port,$result, $errorCode, $errorMessage);
				}
			}
			else
			{
				$retBody['instanceInfo'][] = array($domain,"","","", $retBody['errorCode'], $retBody['errorMessage']);
			}
			$resultObj->body = $retBody;			          
    	}
    	return $resultObj;
    }
    
	/**
	*	域名绑定云服务器多端口
    *	@args = array(
    *		array(
    *       	'lanIps'   => array("ip1","ip2",...),
    *       	'port'     => int,
    *       ),
    *       array(
    *       	'lanIps'   => array("ip1","ip2",...),
    *       	'port'     => int,
    *       ),
    *       ...
    *   );
    */
    public function DomainsBindCvmPorts($instanceId,$args,$Https = false)
    {
    	$rspTemp =array();
    	$rspObj =array();
    	
    	foreach($args as $key => $value)
    	{
    		$apiData = array(
            	'url'=>"/v1/domains/$instanceId/cvm_bind",
            	'method'=>"POST",
            	'body'=>$value,
        	);
			$rspTemp[$key] = self::Send($apiData,$Https);
			$reqId = $rspTemp[$key]->body['requestId']['id'];
			if($this->CheckBindResult($reqId)){
				$rspObj['succ'][$key] = $value;
				$rspObj['succ'][$key]['requestId'] = $reqId;
				continue;
			}
			else{
				$rspObj['fail'][$key] = $value;
				$rspObj['fail'][$key]['requestId'] = $reqId;
				continue;
			}
    	}
    	
        return $rspObj;
    }
    //查询绑定结果
    private function CheckBindResult($reqId)
    {
    	echo "Processing...\n";
    	$res = false;
    	for($i=1; $i<=10; $i++){
	    	$result = ApiRequests::GetRequest($reqId);	    	
			if($result->body['errorCode'] == self::BIND_PROCESSING){
				sleep(5);
			}
			else if ($result->body['errorCode'] == self::BIND_SUCCESS){
				$res = true;
				break;
			}
			else
				break;
    	}
    	return $res;
    }
    
	/**
	*	解绑云服务器域名
    *	@args = array(
    *		'devicesList'   =>array(
    *			array(
    *       		'lanIp'   => string,
    *       		'port'    => int,
    *      		),
    *       	array(
    *       		'lanIp'   => string,
    *       		'port'    => int,
    *       	),
    *       	...
    *       )
    *   );
    */
    public function DomainsUnbindCvm($instanceId,$args,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/domains/$instanceId/cvm_unbind",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https);
			
        return $rspObj;
    }
    
    
    public function GetRequestId($requestId)
    {
    	$apiData = array(
            'url'=>"/v1/requests/$requestId",
            'method'=>'GET',
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData);
		return $rspObj;
    }
    
	 public function GetCreateDomainRet($requestId)
    {
    	$getReqObj = self::GetRequestId($requestId);
    	$getReqBody = $getReqObj->getBody();
    	$getReqBody['instanceInfo'] = array();
    	if(200 == $getReqBody['httpCode'])
    	{
    		$getReqBody['instanceInfo'][] = array('domain','result');
    		foreach($getReqBody['rsp']['instances'] as $key=>$val)
    		{
    			$instanceId = $val['instanceId'];
    			$getDomainInfoObj = self::GetDomainInstance($instanceId);
   				$getDomainInfoBody = $getDomainInfoObj->getBody();
   				if(200 == $getDomainInfoBody['httpCode'])
   				{
   					$getReqBody['instanceInfo'][] = array(
   						$getDomainInfoBody['instanceInfo']['domain'],
   						"SUCC",
   					);
   				}
    		}
    		$getReqObj->body = $getReqBody;
    	}
    	return $getReqObj;
    }
    
	/**
	* 	cmd删除域名
    * 	$args=array(
    * 		'domain' => string,删除域名
    * 	)      
    */
    public function DeleteDomainQc($args,$Https = false)
    {
    	$instanceNames = $args['domain'];
    	$getInstansId = self::GetIdbyDomains($instanceNames);
    	$getInstansIdBody = $getInstansId->getBody();
    	if(200 == $getInstansIdBody['httpCode'])
    	{
			$instanceId = isset($getInstansIdBody['instanceIds'][$instanceNames])?$getInstansIdBody['instanceIds'][$instanceNames] : 1;
	    	$retDeletedomain = self::DeleteDomain($instanceId);
	    	return $retDeletedomain;    		
    	}
    	else
    	{
    		return $getInstansId;
    	}
    }
    
	public function GetDeleteDomainRet($requestId)
    {
		$rspObj = self::GetRequestId($requestId);
		$retBody = $rspObj->getBody();
		$retBody['instanceInfo'] = array();
		if(200 == $retBody['httpCode'])
		{
			$retBody['instanceInfo'][] = array('actions','return');
			$retBody['instanceInfo'][] = array('delete domain','SUCC');
			
		}
		$rspObj->body = $retBody;
		return $rspObj;
    }
    
	/**
	*	cmd查询域名列表
	*	@args = array(
    *       'domain'   => string,查询的domain,逗号分割
    *   );
    */
    public function GetDomainList($args)
    {
    	if($args['domain'])
    	{
	    	$rspObj = self::GetIdbyDomains($args['domain']);
	    	$retBody = $rspObj->getBody();
			$retBody['instanceInfo'] = array();
			if(200 == $retBody['httpCode'] && count( $retBody['instanceIds'] ) >0)
			{
				$retBody['instanceInfo'][] = array('domain','status','bindnum','errorCode','errorMessage');
				$rspArr = $retBody['instanceIds'];
				foreach($rspArr as $key => $instanceId)
				{
					$instanceArr = self::GetDomainInstance($instanceId);
					$instanceObj = $instanceArr->getBody();
					if(200 == $instanceObj['httpCode'])
					{
						switch ($instanceObj['instanceInfo']['status'])
						{
							case 0:
								$status = "no bind";
								$bindnum = 0;
								break;
							case 1:
								$status = "bind cvm";
								$bindnum = count($instanceObj['instanceInfo']['devicesList']);
								break;
							case 2:
								$status = "bind cee";
								$bindnum = 1;
								break;
							default:
								$status = "";
								$bindnum = "";
						}
						$retBody['instanceInfo'][] = array(
							$instanceObj['instanceInfo']['domain'],
							$status,
							$bindnum,
							"",
							""
						);
					}
					else
                        $retBody['instanceInfo'][] = array("","","",$instanceObj['errorCode'],$instanceObj['errorMessage']);
				}
			}
			$rspObj->body = $retBody;
    	}
    	else
    	{
    		$rspObj = self::GetDomains(array());
	    	$retBody = $rspObj->getBody();
			$retBody['instanceInfo'] = array();
			if(200 == $retBody['httpCode'] && $retBody['num'] >0)
			{
				$retBody['instanceInfo'][] = array('domain','status','bindnum','errorCode','errorMessage');
				$rspArr = $retBody['instances'];
				foreach($rspArr as $key => $row)
				{
					$instanceId = $row['instanceId'];
					$instanceArr = self::GetDomainInstance($instanceId);
					$instanceObj = $instanceArr->getBody();
					if(200 == $instanceObj['httpCode'])
					{
						switch ($instanceObj['instanceInfo']['status'])
						{
							case 0:
								$status = "no bind";
								$bindnum = 0;
								break;
							case 1:
								$status = "bind cvm";
								$bindnum = count($instanceObj['instanceInfo']['devicesList']);
								break;
							case 2:
								$status = "bind cee";
								$bindnum = 1;
								break;
							default:
								$status = "";
								$bindnum = "";
						}
						$retBody['instanceInfo'][] = array(
							$instanceObj['instanceInfo']['domain'],
							$status,
							$bindnum,
							"",
							""
						);
					}
					else
                        $retBody['instanceInfo'][] = array("","","",$instanceObj['errorCode'],$instanceObj['errorMessage']);
				}
			}
			$rspObj->body = $retBody;
    	}
		return $rspObj;
    }
    
    /**
	*	cmd查询域名绑定情况
	*	@args = array(
    *       'domain'   => string,查询的domain,逗号分割
    *   );
    */
    public function GetDomainBindInfo($args)
    {
    	$rspObj = self::GetIdbyDomains($args['domain']);
	    $retBody = $rspObj->getBody();
		$retBody['instanceInfo'] = array();
		if(200 == $retBody['httpCode'] && count( $retBody['instanceIds'] ) >0)
		{
			$retBody['instanceInfo'][] = array('domain','ip:port','wsname');
			$rspArr = $retBody['instanceIds'];
			foreach($rspArr as $key => $instanceId)
			{
				$instanceArr = self::GetDomainInstance($instanceId);
				$instanceObj = $instanceArr->getBody();
				if(200 == $instanceObj['httpCode'])
				{
					switch ($instanceObj['instanceInfo']['status'])
					{
						case 0:
							$retBody['instanceInfo'][] = array(
								$instanceObj['instanceInfo']['domain'],
								"",
								"",
							);
							break;
						case 1:
							foreach($instanceObj['instanceInfo']['devicesList'] as $key => $value){
								$retBody['instanceInfo'][] = array(
									$instanceObj['instanceInfo']['domain'],
									$value['lanIp'].":".$value['port'],
									"",
								);
							};
							break;
						case 2:
							$retBody['instanceInfo'][] = array(
								$instanceObj['instanceInfo']['domain'],
								"",
								$instanceObj['instanceInfo']['wsName'],
							);
							break;
						default:
							$retBody['instanceInfo'][] = array(
								$instanceObj['instanceInfo']['domain'],
								"",
								"",
							);
					}
				}
			}
		}
		$rspObj->body = $retBody;
		return $rspObj;
    }
 
	 /**
	 *	 cmd域名解绑cvm
     * 	$args=array(
     * 		'domain'	=>	string, 解绑的域名，支持一个
     * 		'ip' 		=>  string, 解绑域名下的该ip，支持批量,逗号分隔
     * 		'port'		=>	int,    解绑域名下ip对应的port,支持批量，逗号分隔
     * 	)
     */
    public function UnbindDomainQc($args,$Https = false)
    {
    	$domain = $args['domain'];
    	$getInstansId = self::GetIdbyDomains($domain);
    	$getInstansIdBody = $getInstansId->getBody();
    	if(200 == $getInstansIdBody['httpCode'])
    	{
			$instanceId = isset($getInstansIdBody['instanceIds'][$domain])?$getInstansIdBody['instanceIds'][$domain] : 1;			
			$device['devicesList'] = array();
			$ipArr = explode(",",$args['ip']);
			$portArr = explode(",",$args['port']);
			$ipNum = count($ipArr);
			$portNum = count($portArr);
			for($i=0; $i<$ipNum; $i++)
			{
				for($j=0; $j<$portNum; $j++)
				{
					$device['devicesList'][] = array(
						'lanIp'   => $ipArr[$i],
	    	       		'port'    => (int)$portArr[$j],
					);
				}
			}
	    	$retUnbinddomain = self::DomainsUnbindCvm($instanceId,$device);
	    	return $retUnbinddomain;		
    	}
    	else
    	{
    		return $getInstansId;
    	}
    }
    
	public function GetUnbindDomainRet($requestId)
    {
		$rspObj = self::GetRequestId($requestId);
		$retBody = $rspObj->getBody();
		$retBody['instanceInfo'] = array();
		if(202 != $retBody['httpCode'])
		{
			$retBody['httpCode'] = '200';
			$errorCode = $retBody['errorCode'];
			$errorMessage = $retBody['errorMessage'];
			$retBody['instanceInfo'][] = array('domain','ip','port','result','errorCode','errorMessage');
			$instanceId = $retBody['rsp']['instanceId'];
			$getDomain = self::GetDomainInstance($instanceId);
			$domainInfo = $getDomain->getBody();
			$domain = $domainInfo['instanceInfo']['domain'];
			
			$args = $retBody['req']['body']['devicesList'];
			if(!empty($args))
			{
				foreach($args as $key => $value)
				{
					$ip = $value['lanIp'];
					$port = $value['port'];
					if(!in_array($value,$domainInfo['instanceInfo']['devicesList']))
					{
						$result = "UNBIND SUCC";
						$errorCode = "200";
						$errorMessage = "OK";
					}
					else
					{
						$result = "UNBIND FAIL";
					}
					$retBody['instanceInfo'][] = array($domain,$ip,$port,$result,$errorCode,$errorMessage);
				}
			}
			else
			{
				$retBody['instanceInfo'][] = array($domain,"","","", $retBody['errorCode'], $retBody['errorMessage']);
			}
			$rspObj->body = $retBody;			
		}
		return $rspObj;
    }
}
