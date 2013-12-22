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


class ApiCdbs extends ApiBaseOpencloud
{
	const SUCCESS    = 200;
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
            'url'=>"/v1/cdbs/query_instance_id?instancenames=$args",
            'method'=>"GET",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	查询CDB实例列表
    *	@args = array(
    *       'instanceids'   => string,
    *       'offset'        => int,
    *       'limit'         => int,
    *   );
    */
    public function GetCdbs($args,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/cdbs",
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
	*	查询CDB实例详细信息
    */
    public function GetCdbInstance($instanceId,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/cdbs/$instanceId",
            'method'=>"GET",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
    /**
	*	获取CDB慢查询日志
	*   @args = array(
    *       'offset'   	   => int,
    *       'limit'        => int,
    *       'date'         => yyyy-mm-dd,
    *   );
    */
    public function GetCdbSlowLog($instanceId,$args,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/cdbs/$instanceId/slow_query_log",
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
	*	查询CDB统计信息
    */
    public function GetCdbStatic($instanceId,$Https = false)
    {
        $apiData = array(
            'url'=>"/v1/cdbs/$instanceId/static",
            'method'=>"GET",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	CDB实例申请
    *	@args = array(
    *       'flavor' = array(
    *       	'capacity' => int,
    *       ),
    *       'password' => string,
    *       'charset'  => string,
    *       'number'   => int,
    *   );
    */
    public function CreateCdb($args)
    {
    	$apiData = array(
            'url'=>"/v1/cdbs",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https = true);
		
        return $rspObj;
    }
    
	/**
	*	CDB字符集修改
    *	@args = array(
    *       'charset' => string,
    *   );
    */
    public function CdbModifyCharset($instanceId,$args,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cdbs/$instanceId/modify_charset",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	CDB名称修改
    *	@args = array(
    *       'instanceName' => string,
    *   );
    */
    public function CdbModifyInstanceName($instanceId,$args,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cdbs/$instanceId/modify_instance_name",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
    /**
	*	CDB密码重置
	*	@args = array(
	*		'password' => string,
	*   )
    */
    public function CdbResetPassword($instanceId,$args)
    {
    	$apiData = array(
            'url'=>"/v1/cdbs/$instanceId/reset_password",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https = true);
		
        return $rspObj;
    }
    
   	/**
	*	CDB信息修改
    *	@args = array(
    *       'instanceName' => string,非必传
    *       'charset' => string,非必传
    *   );
    */    
    public function CdbInfoModify($instanceId,$args,$Https = false)
    {
    	$para = array();   	
    	if(!empty($args['instanceName'])){
    		$para['instanceName'] = $args['instanceName'];
    		$apiData = array(
	            'url'=>"/v1/cdbs/$instanceId/modify_instance_name",
	            'method'=>"POST",
	            'body'=>$para,
	        );
	        $rspTemp = self::Send($apiData,$Https);
	        if(self::SUCCESS == $rspTemp->getErrorCode()){
	        	$rspObj['instanceName']['result'] = "succ";
	        	$rspObj['instanceName']['errorCode'] = 200;
	        	$rspObj['instanceName']['errorMessage'] = "OK";
	        }
	        else{
	        	$rspObj['instanceName']['result'] = "fail";
	        	$rspObj['instanceName']['errorCode'] = $rspTemp->getErrorCode();
	        	$rspObj['instanceName']['errorMessage'] = $rspTemp->getErrorMsg();
	        }
	        unset($para['instanceName']);
    	}
    	if(!empty($args['charset'])){
    		$para['charset'] = $args['charset'];
    		$apiData = array(
	            'url'=>"/v1/cdbs/$instanceId/modify_charset",
	            'method'=>"POST",
	            'body'=>$para,
	        );
    	 	$rspTemp = self::Send($apiData,$Https);
	        if(self::SUCCESS == $rspTemp->getErrorCode()){
	        	$rspObj['charset']['result'] = "succ";
	        	$rspObj['charset']['errorCode'] = 200;
	        	$rspObj['charset']['errorMessage'] = "OK";
	        }
	        else{
	        	$rspObj['charset']['result'] = "fail";
	        	$rspObj['charset']['errorCode'] = $rspTemp->getErrorCode();
	        	$rspObj['charset']['errorMessage'] = $rspTemp->getErrorMsg();
	        }
    	}
    	
    	return $rspObj;	
    }
    
	/**
	*	CDB文件导入
    *	@args = array(
    *       'filePath' => string,
    *       'password' => string,
    *       'database' => string,
    *   );
    */
    public function CdbImport($instanceId,$args,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cdbs/$instanceId/import",
            'method'=>"POST",
            'body'=>$args,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	CDB终止导入
    */
    public function CdbImportStop($instanceId,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cdbs/$instanceId/import_stop",
            'method'=>"POST",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
	/**
	*	CDB退还
    */
    public function DeleteCdb($instanceId,$Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cdbs/$instanceId",
            'method'=>"DELETE",
            'body'=>NULL,
        );
		$rspObj = self::Send($apiData,$Https);
		
        return $rspObj;
    }
    
    /**
    *   cmd 删除cdb实例
    * 	@args=array(
    * 		'instanceName'=>'xxxx',
    * )
    */
    public function DeleteCdbQc($args,$Https = false)
    {
    	$instanceNames = $args['instanceName'];
    	$getInstansId = self::GetIdbyInstances($instanceNames);
    	$getInstansIdBody = $getInstansId->getBody();
    	if(200 == $getInstansIdBody['httpCode'])
    	{
			$instanceId = isset($getInstansIdBody['instanceIds'][$instanceNames])?$getInstansIdBody['instanceIds'][$instanceNames] : 1;
	    	$retDeletecdb = self::DeleteCdb($instanceId);
	    	return $retDeletecdb;    		
    	}
    	else
    	{
    		return $getInstansId;
    	}
    }
    
	/**
	*	查询CDB可导入文件列表
    */
    public function GetCdbImportFile($Https = false)
    {
    	$apiData = array(
            'url'=>"/v1/cdbs/query_file_import",
            'method'=>"GET",
            'body'=>NULL,
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
    
    public function GetCreateCdbRet($requestId)
    {
		$rspObj = self::GetRequestId($requestId);
		$retBody = $rspObj->getBody();
		$retBody['instanceInfo'] = array();
		if(200 == $retBody['httpCode'])
		{
			$retBody['instanceInfo'][] = array('instanceName','vip','vport','capacity','charset');
			$rspArr = $retBody['rsp']['instances'];
			foreach($rspArr as $key => $row)
			{
				$instanceId = $row['instanceId'];
				$instanceArr = self::GetCdbInstance($instanceId);
				$instanceObj = $instanceArr->getBody();
				if(200 == $instanceObj['httpCode'])
				{
					$retBody['instanceInfo'][] = array(
						$instanceObj['instanceInfo']['instanceName'],
						$instanceObj['instanceInfo']['vip'],
						$instanceObj['instanceInfo']['vport'],
						$instanceObj['instanceInfo']['capacity'],
						$instanceObj['instanceInfo']['charset'],
					);
				}
			}
		}
		$rspObj->body = $retBody;
		return $rspObj;
    }
    
	public function GetDeleteCdbRet($requestId)
    {
		$rspObj = self::GetRequestId($requestId);
		$retBody = $rspObj->getBody();
		$retBody['instanceInfo'] = array();
		if(200 == $retBody['httpCode'])
		{
			$retBody['instanceInfo'][] = array('actions','return');
			$retBody['instanceInfo'][] = array('delete cdb','SUCC');
			
		}
		$rspObj->body = $retBody;
		return $rspObj;
    }
    
    /**
	*	cmd查询cdb实例列表
	*	@args = array(
	*		names => string,查询的instancenames,逗号分割
	*	)
    */
    public function GetCdbList($args)
    {
    	if($args['names'])
    	{
	    	$rspObj = self::GetIdbyInstances($args['names']);
	    	$retBody = $rspObj->getBody();
			$retBody['instanceInfo'] = array();
			if(200 == $retBody['httpCode'] && count( $retBody['instanceIds'] ) >0)
			{
				$retBody['instanceInfo'][] = array('instanceName','vip','vport','capacity','charset','status','errorCode','errorMessage');
				$rspArr = $retBody['instanceIds'];
				foreach($rspArr as $key => $instanceId)
				{
					$instanceArr = self::GetCdbInstance($instanceId);
					$instanceObj = $instanceArr->getBody();
					if(200 == $instanceObj['httpCode'])
					{
						$status = $this->_cdbStatus[ $instanceObj['instanceInfo']['status'] ];
						$status = !$status ? 'running' : $status;
						$retBody['instanceInfo'][] = array(
							$instanceObj['instanceInfo']['instanceName'],
							$instanceObj['instanceInfo']['vip'],
							$instanceObj['instanceInfo']['vport'],
							$instanceObj['instanceInfo']['capacity'],
							$instanceObj['instanceInfo']['charset'],
							$status,
							"",
							""
						);
					}
                    else
                    {
                        $retBody['instanceInfo'][] = array("","","","","","",$instanceObj['errorCode'],$instanceObj['errorMessage']);
                    }
				}
			}
			$rspObj->body = $retBody;
    	}
    	else
    	{
    		$rspObj = self::GetCdbs( array() );
	    	$retBody = $rspObj->getBody();
			$retBody['instanceInfo'] = array();
			if(200 == $retBody['httpCode'] && $retBody['num'] >0)
			{
				$retBody['instanceInfo'][] = array('instanceName','vip','vport','capacity','charset','status','errorCode','errorMessage');
				$rspArr = $retBody['instances'];
				foreach($rspArr as $key => $row)
				{
					$instanceId = $row['instanceId'];
					$instanceArr = self::GetCdbInstance($instanceId);
					$instanceObj = $instanceArr->getBody();
					if(200 == $instanceObj['httpCode'])
					{
						$status = $this->_cdbStatus[ $instanceObj['instanceInfo']['status'] ];
						$status = !$status ? 'running' : $status;
						$retBody['instanceInfo'][] = array(
							$instanceObj['instanceInfo']['instanceName'],
							$instanceObj['instanceInfo']['vip'],
							$instanceObj['instanceInfo']['vport'],
							$instanceObj['instanceInfo']['capacity'],
							$instanceObj['instanceInfo']['charset'],
							$status,
							"",
							""
						);
					}
					else
						 $retBody['instanceInfo'][] = array("","","","","","",$instanceObj['errorCode'],$instanceObj['errorMessage']);
				}
			}
			$rspObj->body = $retBody;
    	}
		return $rspObj;
    }
    
    /**
    *	cmd修改cdb实例信息
    * 	$args = array(
    * 		'instanceName'    => 'oldcdbname',
    * 		'newInstanceName' => 'newcdbname',
    * 		'newCharset'      => 'utf8',
    * 		'newPassword'     => 'barry123',
    * )
    */
    public function UpdateCdb($args)
    {
    	$rspObj = new HttpRsp();
    	$body = array(
    		'httpCode'     => 200,
    		'errorCode'    => 200,
    		'errorMessage' => '200 OK',
    	    'instanceInfo' => array()   	
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
    	$paramTag = isset($args['newCharset']) ? true : $paramTag;
    	$paramTag = isset($args['newPassword']) ? true : $paramTag;
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
    	
    	if(isset($args['newCharset']))
    	{
    		$argsTemp = array('charset'=>$args['newCharset']);
	    	$upCharsetObj = self::CdbModifyCharset($instId,$argsTemp);
	    	$upCharsetRet = $upCharsetObj->getBody();
	    	$body['requestId']['id'] .=  $upCharsetRet['requestId']['id'].",";
    	}
    	
    	if(isset($args['newPassword']))
    	{
    		$argsTemp = array('password'=>$args['newPassword']);
	    	$upPasswordObj = self::CdbResetPassword($instId,$argsTemp ,true);
	    	$upPasswordRet = $upPasswordObj->getBody();
	    	$body['requestId']['id'] .=  $upPasswordRet['requestId']['id'].",";
    	}
    	
    	if(isset($args['newInstanceName']))
    	{
    		$argsTemp = array('instanceName'=>$args['newInstanceName']);
	    	$upNameObj = self::CdbModifyInstanceName($instId,$argsTemp);
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
    		'reset_password' => 'update password',
    		'modify_charset'=>'update charset',
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
	    	$action = $actionRe[end($urlArr)] ? $actionRe[end($urlArr)] : "update cdb";
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
    * 	cmd查询cdb统计信息
    * 	$args = array(
    *		'instanceName' => string,实例名     
    * 	)
    */
	public function GetCdbStaticInfo($args = array())
    {
    	
    	if(count($args) >0)
    	{
    		$args = $args['instanceName'];
    		$getCdbInIdObj = self::GetIdbyInstances($args);
   			$getCdbInIdBody = $getCdbInIdObj->getBody();
   			if(200 == $getCdbInIdBody['httpCode'])
   			{
   				$instanceId = 1;
   				
   				$tempArgs = array('instanceids'=>'');
   				if( count($getCdbInIdBody['instanceIds']) > 0 )
   				{
   					$instanceId = $getCdbInIdBody['instanceIds'][$args];
   				}
   				$cdbStaticObj = self::GetCdbStatic($instanceId);
   				$cdbStaticBody = $cdbStaticObj->getbody();
   				$cdbStaticBody['instanceInfo'][] = array('Total space','Used sapce','Visits/minute','Slow queries/minute');
   				if(200 == $cdbStaticBody['httpCode'])
   				{
                    $cdbStaticBody['instanceInfo'][] = array(
                    	round($cdbStaticBody['stat']['allspace']/1024/1024, 2)."MB",   	
                    	round($cdbStaticBody['stat']['usespace']/1024/1024, 2)."MB",
                    	$cdbStaticBody['stat']['querys'],
                    	$cdbStaticBody['stat']['slowquerys'],
                    ); 					
   				}
   				$cdbStaticObj->body = $cdbStaticBody;
   				return $cdbStaticObj;
   			}
   			else
   			{
   				return $getCdbInIdObj;
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
}
