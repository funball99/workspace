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


class ApiCmems extends ApiBaseOpencloud
{  
	private $_cdbStatus = array(
		0=>'creating',
		1=>'running'
	);
	 
  	function __construct($host,$secretId,$secretKey)
  	{
  		ApiBaseOpencloud::setPara($host,$secretId,$secretKey);
  	}
	
    /**
	*	根据实例查实例ID
	*	@args : string,查询的instancenames,逗号分割
    */
    public function GetIdbyInstances($args,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/cmems/query_instance_id?instancenames=$args",
            'method'=>"GET",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
	
	/**
	*	查询CMEM实例列表
    *	@args = array(
    *       'instanceids'   => string,
    *       'offset'        => int,
    *       'limit'         => int,
    *   );
    */
    public function GetCmems($args,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/cmems",
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
	*	查询CMEM实例详细信息
    */
    public function GetCmemInstance($instanceId,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/cmems/$instanceId",
            'method'=>"GET",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
    /**
	*	CMEM申请
	*	@args = array(
	*		'flavor' = array(
	*			'capacity'  => int,
	*		),
	*		'number'  => int,
	*	);
    */
    public function CreateCmem($args,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/cmems",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	CMEM实例expire修改
    *	@args = array(
    *       'expire'  => int,
    *   );
    */
    public function CmemModifyExpire($instanceId,$args,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cmems/$instanceId/modify_expire",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	CMEM实例名称修改
    *	@args = array(
    *       'instanceName'  => string,
    *   );
    */
    public function CmemModifyInstanceName($instanceId,$args,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cmems/$instanceId/modify_instance_name",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }   
    
	/**
	*	CMEM文件导入
    *	@args = array(
    *       'filePath' => string,
    *   );
    */
    public function CmemImport($instanceId,$args,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cmems/$instanceId/import",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	CMEM终止导入
    */
    public function CmemImportStop($instanceId,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cmems/$instanceId/import_stop",
            'method'=>"POST",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	CMEM退还
    */
    public function DeleteCmem($instanceId,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cmems/$instanceId",
            'method'=>"DELETE",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	查询CMEM可导入文件列表
    */
    public function GetCmemImportFile($Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cmems/query_file_import",
            'method'=>"GET",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	查询CMEM统计信息
    */
    public function GetCmemStatic($instanceId,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cmems/$instanceId/static",
            'method'=>"GET",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
    /**
	*	cmd查询cmem实例列表
	*	@args = array(
	*		names => string,查询的instancenames,逗号分割
	*	)
    */ 
    public function GetCmemInfo($args)
    {
    	$tempArgs = array();
   		if($args['names'])
   		{
   			$getCmemInIdObj = self::GetIdbyInstances($args['names']);
   			$getCmemInIdBody = $getCmemInIdObj->getBody();
   			if(200 == $getCmemInIdBody['httpCode'])
   			{
   				$tempArgs = array('instanceids'=>'');
   				if( count($getCmemInIdBody['instanceIds']) > 0 )
   				{
   					foreach($getCmemInIdBody['instanceIds'] as $key=>$insId)
   					{
   						$tempArgs['instanceids'] .= $insId.",";
   					}
   					$tempArgs['instanceids'] = rtrim($tempArgs['instanceids'] , ',');
   				}
                else
                {
                    $tempArgs['instanceids'] = 1;
                }
   			}
   			else
   			{
   				return $getCmemInIdObj;
   			}
   		}
   		
   		$cmemInfoObj =  self::GetCmems($tempArgs);
   		$cmemInfoBody = $cmemInfoObj->getBody();
        if(200 == $cmemInfoBody['httpCode'])
   		{
   			$body = $cmemInfoBody;
   			$body['instanceInfo'][] = array('instanceName','vip','vport','capacity','expire','status','errorCode','errorMessage');
   			if(count($cmemInfoBody['instances']) > 0)
   			{		
   				foreach($cmemInfoBody['instances'] as $key=>$val)
   				{
   					$instanceId = (int)$val['instanceId'];
   					$getCmemInfoObj = self::GetCmemInstance($instanceId);
   					$getCmemInfoBody = $getCmemInfoObj->getBody();
   					if(200 == $getCmemInfoBody['httpCode'])
   					{
   						$expire = 0 == $getCmemInfoBody['instanceInfo']['expire'] ? 'no' : 'yes';
   						$status = $this->_cdbStatus[ $instanceObj['instanceInfo']['status'] ];
						$status = !$status ? 'running' : $status;
   						$body['instanceInfo'][] = array(
   							$getCmemInfoBody['instanceInfo']['instanceName'],
   							$getCmemInfoBody['instanceInfo']['vip'],
   							$getCmemInfoBody['instanceInfo']['vport'],
   							$getCmemInfoBody['instanceInfo']['capacity']."GB",
   							$expire,
   							$status,
   							"",
   							"",
   						);
   					}
   					else
   						$body['instanceInfo'][] = array("","","","","","",$getCmemInfoBody['errorCode'],$getCmemInfoBody['errorMessage']);
   				}
   			}
   			$cmemInfoObj->body = $body;
   		}
   		return $cmemInfoObj;
    }
	
    
    /**
    * 	cmd查询cmem统计信息
    * 	$args = array(
    *		'instanceName' => string,实例名     
    * 	)
    */
    public function GetCmemStaticInfo($args = array())
    {
    	
    	if(count($args) >0)
    	{
    		$args = $args['instanceName'];
    		$getCmemInIdObj = self::GetIdbyInstances($args);
   			$getCmemInIdBody = $getCmemInIdObj->getBody();
   			if(200 == $getCmemInIdBody['httpCode'])
   			{
   				$instanceId = 1;
   				
   				$tempArgs = array('instanceids'=>'');
   				if( count($getCmemInIdBody['instanceIds']) > 0 )
   				{
   					$instanceId = $getCmemInIdBody['instanceIds'][$args];
   				}
   				$cmemStaticObj = self::GetCmemStatic($instanceId);
   				$cmemStaticBody = $cmemStaticObj->getbody();
   				$cmemStaticBody['instanceInfo'][] = array('Total space','Used sapce','Visits/second');
   				if(200 == $cmemStaticBody['httpCode'])
   				{
                    $cmemStaticBody['instanceInfo'][] = array(
                    	round($cmemStaticBody['stat']['all_size']/1024/1024, 2)."MB",
                    	round($cmemStaticBody['stat']['data_size']/1024/1024, 2)."MB",
                    	$cmemStaticBody['stat']['total'],
                    );

   					$cmemStaticObj->body = $cmemStaticBody;
   					return $cmemStaticObj;
   				}
				$cmemStaticObj->body = $cmemStaticBody;
   				return $cmemStaticObj;
   			}
   			else
   			{
   				return $getCmemInIdObj;
   			}
    	}
    	else
    	{
    		$rspObj = new HttpRsp();
	    	$body = array(
	    		'httpCode'     => 400,
	    		'errorCode'    => 400.001,
	    		'errorMessage' => 'instanceName is NULL',
	    	    'instanceInfo' => array()
	    	);
	    	$rspObj->body = $body;
    	}
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
    
    public function GetCreateCmemRet($requestId)
    {
    	$getReqObj = self::GetRequestId($requestId);
    	$getReqBody = $getReqObj->getBody();
    	$getReqBody['instaceInfo'] = array();
    	if(200 == $getReqBody['httpCode'])
    	{
    		$getReqBody['instanceInfo'][] = array('instanceName','vip','vport','capacity','expire');
    		foreach($getReqBody['rsp']['instances'] as $key=>$val)
    		{
    			$instanceId = $val['instanceId'];
    			$getCmemInfoObj = self::GetCmemInstance($instanceId);
   				$getCmemInfoBody = $getCmemInfoObj->getBody();
   				if(200 == $getCmemInfoBody['httpCode'])
   				{
   					$expire = 0 == $getCmemInfoBody['instanceInfo']['expire'] ? 'no' : 'yes';
   					$getReqBody['instanceInfo'][] = array(
   						$getCmemInfoBody['instanceInfo']['instanceName'],
   						$getCmemInfoBody['instanceInfo']['vip'],
   						$getCmemInfoBody['instanceInfo']['vport'],
   						$getCmemInfoBody['instanceInfo']['capacity']."GB",
   						$expire,
   					);
   				}
    		}
    		$getReqObj->body = $getReqBody;
    	}
    	return $getReqObj;
    }

    
	/**
    * 	cmd修改cmem实例信息
    *	args=array(
    *		'instanceName'      => 'xxxxxx',
    *		'newInstanceName'   => 'dddd',
    *		'expire'            => 'no',
    *	)  
    */
    public function UpdateCmem($args)
    {
    	$rspObj = new HttpRsp();
    	$body = array(
    		'httpCode'     => 200,
    		'errorCode'    => 200,
    		'errorMessage' => '200 OK'
    	);
    	
    	//check params
    	if(!isset($args['instanceName']))
    	{
    		$body['httpCode'] = 400;
    		$body['errorCode'] = 400.001;
    		$body['errorMessage'] = 'instanceName is NULL';
    		$rspObj->body = $body;
    		return $rspObj;
    	}
    	$paramTag = false;
    	$paramTag = isset($args['newInstanceName']) ? true : $paramTag;
    	$paramTag = isset($args['expire']) ? true : $paramTag;
    	if(!$paramTag)
    	{
    		$body['httpCode'] = 400;
    		$body['errorCode'] = 400.001;
    		$body['errorMessage'] = 'update options is empty';
    		$rspObj->body = $body;
    		return $rspObj;
    	}
    	//get instanceId
    	$getIdObj = self::GetIdbyInstances($args['instanceName']);
    	$getIdRet = $getIdObj->getBody();
    	if(200 == $getIdRet['httpCode'])
    	{
    		$instId = $getIdRet['instanceIds'][ $args['instanceName'] ];
    	}
    	else
    	{
    		return $getIdObj;
    	}
    	
    	if(!$instId)
    	{
    		$body['httpCode'] = 404;
    		$body['errorCode'] = 404.010;
    		$body['errorMessage'] = "{$args['instanceName']} is not exists";
    		$rspObj->body = $body;
    		return $rspObj;
    	}
    	
    	$body['instanceInfo'][] = array('actions','result'); 
    	
    	if(isset($args['expire']))
    	{
    		$expire = 'yes' == $args['expire'] ? 1 : 0;
    		$argsTemp = array('expire'=>$expire);
	    	$upExpireObj = self::CmemModifyExpire($instId,$argsTemp);
	    	$upExpireRet = $upExpireObj->getBody();
	    	$body['requestId']['id'] .=  $upExpireRet['requestId']['id'].",";
    	}
    	
        if(isset($args['newInstanceName']))
    	{
    		$argsTemp = array('instanceName'=>$args['newInstanceName']);
	    	$upNameObj = self::CmemModifyInstanceName($instId,$argsTemp);
	    	$upNameRet = $upNameObj->getBody();
	    	$body['requestId']['id'] .=  $upNameRet['requestId']['id'].",";
    	}
    	
    	$rspObj->body = $body;
    	return $rspObj;
    }
    
    public function GetRequestsRet($requestIds)
    {
    	$actionRe = array(
    		'modify_instance_name' => 'update name',
    		'modify_expire' => 'update expire',
    	);
    	$rspObj = new HttpRsp();
    	$body = array(
    		'httpCode'     => 202,
    		'errorCode'    => 202,
    		'errorMessage' => '200 OK',
    		'instanceInfo' => array()
    	);
    	$requestIds = rtrim($requestIds,',');
    	$requestArr = explode(',',$requestIds);
    	$tag = 1;
    	$ret = array(array('actions','result'));
    	foreach($requestArr as $requestId)
    	{
	    	$rspObj = self::GetRequestId($requestId);
	    	$repRet = $rspObj->getBody();
	    	$url = $repRet['req']['url'];
	    	$urlArr = explode('/',$url);
	    	$action = $actionRe[end($urlArr)] ? $actionRe[end($urlArr)] : "update cmem";
	    	if(200 == $repRet['httpCode'])
	    	{
	    		$body['httpCode'] = 200;
	    		$ret[] = array("{$action}",'SUCC');
	    	}
	    	elseif(202 == $repRet['httpCode'])
	    	{
	    		$tag = 0;
	    		break;
	    	}
	    	else
	    	{
	    		$body['httpCode'] = 200;
	    		if('401.102' == $repRet['errorCode'])
	    		{
	    			$ret[] = array("{$action}",$repRet['errorMessage']);
	    		}
	    		else
	    		{
	    			$ret[] = array("{$action}",'FAIL');
	    		}
	    	}
    	}
    	if($tag)
    	{
    		$body['instanceInfo'] = $ret;
    	}
    	else
    	{
    		$body['httpCode'] = 202;
    	}
    	
    	$rspObj->body = $body;
    	return $rspObj;
    }
    
    /**
    * 	cmd查询操作结果
    * 	$args = array(
    *		'requestId' => $requestId,
    *		'options'  => $options        
    * 	)
    */
    public function GetRequestRet($args)
    {
    	$requestId = $args['requestId'];
    	$options = $args['options'];
    	$ret = array();
    	$rspObj = self::GetRequestId($requestId);
    	$repRet = $rspObj->getBody();
    	if(200 == $repRet['httpCode'])
    	{
    		$ret = array("update {$options}",'SUCC');
    	}
    	elseif(202 == $repRet['httpCode'])
    	{
    		sleep(5);
    		$rspObj = self::GetRequestId($requestId);
    		$repRet = $rspObj->getBody();
    		if(200 == $repRet['httpCode'])
    		{
    			$ret = array("update {$options}",'SUCC');
    		}	    			
    	}
    	else
    	{
    		$ret = array("update {$options}",'FAIL');
    	}
    	return $ret;
    }
    
	/**
	*	cmd删除cmem实例
    * 	$args=array(
    * 		'instanceName' => string, 删除实例名
    * 	)
    * 
    */
    public function DeleteCmemQc($args,$Https = false)
    {
    	$instanceNames = $args['instanceName'];
    	$getInstansId = self::GetIdbyInstances($instanceNames);
    	$getInstansIdBody = $getInstansId->getBody();
    	if(200 == $getInstansIdBody['httpCode'])
    	{
			$instanceId = isset($getInstansIdBody['instanceIds'][$instanceNames])?$getInstansIdBody['instanceIds'][$instanceNames] : 1;
	    	$retDeletecdb = self::DeleteCmem($instanceId);
	    	return $retDeletecdb;    		
    	}
    	else
    	{
    		return $getInstansId;
    	}
    }
    
	public function GetDeleteCmemRet($requestId)
    {
		$rspObj = self::GetRequestId($requestId);
		$retBody = $rspObj->getBody();
		$retBody['instaceInfo'] = array();
		if(200 == $retBody['httpCode'])
		{
			$retBody['instanceInfo'][] = array('actions','return');
			$retBody['instanceInfo'][] = array('delete cmem','SUCC');
			
		}
		$rspObj->body = $retBody;
		return $rspObj;
    }
}
