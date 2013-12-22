<?php
$baseDir = dirname(__FILE__) ;
require_once "{$baseDir}/common/Conf.class.php";
require_once "{$baseDir}/common/cmdHelp.class.php";
error_reporting(0);

class CloudCmd{

    const C_ENV_HOST = 'TC_OPENCLOUD_ENDPOINT';
    const C_ENV_SECRETID = 'TC_OPENCLOUD_SECRETID';
    const C_ENV_SECRETKEY = 'TC_OPENCLOUD_SECRETKEY';
    
    const C_CMD_HOST = 'host';
    const C_CMD_SECRETID = 'secretid';
    const C_CMD_SECRETKEY = 'secretkey';
    
    
    private static $_host = '';
    private static $_secretId = '';
    private static $_secretKey = '';
    private static $_iniApp = '';

    public static function loadClass($cloudType, $args) {
        foreach($args as $k=>$v){
            $class = dirname(__FILE__)."/component/{$v}.class.php";
            require_once $class;
        }
    }

    public static function run($cloudType, $opts){
    	if(isset($opts['self']))
    	{
    		$cmdTemp = explode('/',$opts['self']);
			$args = array(
				'cmd'=>array_pop($cmdTemp),
				'tag'=>0,
			);
			$helpRet = CmdHelp::getCmdHelp($args);
			if(0 === $helpRet['retcode'])
			{
				return $helpRet['ret'];
			}
    	}
        if(empty($opts))
            self::_help($cloudType, $opts);
            
        $cmd = '';
        $cmdInput = array(
            'insId' => '',
            'data' => array(),
        );
        foreach($opts as $k => $v){
            switch($k){
                case 'c':
                    $cmd = $v;
                    break;
                case 'n':
                    $cmdInput['data']['number'] = (int)$v;
                    break;
                case 'i':
                    $cmdInput['insId'] = $v;
                    break;
               case 'secretid':
                    self::$_secretId = $v;
                    break;
                case 'secretkey':
                    self::$_secretKey = $v;
                    break;
                case 'endpoint':
                    self::$_host = $v;
                    break;        
                case 'capacity':
                    $cmdInput['data']['flavor']['capacity'] = is_numeric($v) ? (int)$v : $v;
                    break;
                case 'password':
                    $cmdInput['data']['password'] = $v;
                    break;
                case 'charset':
                    $cmdInput['data']['charset'] = $v;
                    break;
                case 'number':
                    $cmdInput['data']['number'] = (int)$v;
                    break;
                case 'names':
                    $cmdInput['data']['names'] = $v;
                    break;
                case 'instanceName':
                    $cmdInput['data']['instanceName'] = $v;
                    break;
                case 'newInstanceName':
                    $cmdInput['data']['newInstanceName'] = $v;
                    break;
                case 'ip':
                	$cmdInput['data']['ip'] = $v;
                    break;
                case 'port':
                	$cmdInput['data']['port'] = $v;
                    break;    
                case 'newCharset':
                    $cmdInput['data']['newCharset'] = $v;
                    break;
                case 'newPassword':
                    $cmdInput['data']['newPassword'] = $v;
                    break;
                case 'expire':
                    $cmdInput['data']['expire'] = $v;
                    break;
                case 'domain':
                	$cmdInput['data']['domain'] = $v;
                    break;
                case 'requestId':
                	$cmdInput['data']['requestId'] = $v;
                	break;
                case 'offset':
                	$cmdInput['data']['offset'] = $v;
                	break;
                case 'begintime':
                	$cmdInput['data']['begintime'] = $v;
                	break;
                case 'endtime':
                	$cmdInput['data']['endtime'] = $v;
                	break;
                case 'uri':
                	$cmdInput['data']['uri'] = $v;
                	break;
                case 'f':    
                    $cmdInput['input_file'] = $v;
                    $cmdInput['data'] = file_get_contents($v);
                    break;
                case 'appflag':
                	self::$_iniApp = $v;
                case 'noheader':
                case 'ips':
                case 'ports':
                case 'self':
                    break;
                case 'version':
                	self::_version($opts);
                	break;
                case 'h':
                case 'help':
                default:
                    self::_help($cloudType, $opts);
                    break;
            }
        }
        self::_missingOperand($opts);
        self::_loadEnv($opts);
        return self::cmd($cloudType, $cmd, $cmdInput, $opts);
    }

    public static function cmd($cloudType, $cmd, $args, $opts) {

        $cmdArr = explode(".",$cmd);
        try{
            $ret = self::_cmd($cmdArr[0],$cmdArr[1], $args['insId'], $args['data'], $opts);
        } catch (Exception $e){
            echo "$e\n";
            self::_help($cloudType, $opts);
        } 
        return $ret;
    }
    
    private static function _missingOperand($opts)
    {
    	$cmdTemp = explode('/',$opts['self']);
    	$cmd = array_pop($cmdTemp);
    	$baseDir = dirname(__FILE__) ;
        $confObj = new Conf();
        $commonConf = $confObj->GetConf("$baseDir/cmdconf.ini");
		$optionArr = $commonConf['required'];
		if( isset($optionArr[ $cmd ]) )
		{
			$ret = "";
			$requiredParam = $optionArr[ $cmd ];
	        $requiredParamArr = explode(',', $requiredParam);
            $tmpArr = $opts;
			unset($tmpArr['self']);
			unset($tmpArr['c']);
			if(count($tmpArr) < 1)
			{
				$ret = "{$cmd}: missing operand\nTry `{$cmd} -h' for more information.\n";
                echo $ret;
                exit('');
			}
			$paramTag = true;
			$param = "";
			foreach($requiredParamArr as $key)
			{
				if(!isset($opts[ $key ]))
				{
					$paramTag = false;
                    break;
				}
				$param = $key;
			}
			if(!$paramTag)
			{
                if(!$param)
                {
                    $ret = "{$cmd}: missing operand\nTry `{$cmd} -h' for more information.\n";
                }
                else
                {
				    $ret = "{$cmd}: missing destination operand after `{$param}'\nTry `{$cmd} -h' for more information.\n";
                }
                echo $ret;
                exit('');
			}
		}
    }
    
    private static function _version($opts)
    {
    	if(isset( $opts['self'] ))
    	{
	    	$cmdTemp = explode('/',$opts['self']);
	    	$cmd = array_pop($cmdTemp);
	    	$ret = "{$cmd} @version 1.0.0\nCopyright (C) 2013, Tencent Corporation. All rights reserved.\n";
    		echo $ret;
            exit('');
    	}
    }
    
    private static function _loadEnv($args){
    
        if(empty($args))    
            self::_help_env();
            
        if(!empty(self::$_iniApp) && is_string(self::$_iniApp)){
        	$baseDir = dirname(__FILE__) ;
	        $confObj = new Conf();
	        if(file_exists("$baseDir/../app.ini")){
	        	$commonConf = $confObj->GetConf("$baseDir/../app.ini");
	        }
	        else{
	        	echo "The multi-apps profile app.ini is not exist!\n";
        		exit('');
	        }
	        	
	        if(is_array($commonConf) && array_key_exists(self::$_iniApp, $commonConf)){
	        	if(is_array($commonConf[self::$_iniApp]) && !empty($commonConf[self::$_iniApp]['secretId']) && !empty($commonConf[self::$_iniApp]['secretKey'])){
					self::$_secretId = $commonConf[self::$_iniApp]['secretId'];
					self::$_secretKey = $commonConf[self::$_iniApp]['secretKey'];
	        	}
	        	else{
	        		echo "Format error: appflag(".self::$_iniApp.")!\n";
        			exit('');
	        	}
	        }
	        else{
	        	echo "The appflag(".self::$_iniApp.") is not exist in app.ini!\n";
        		exit('');
	        }
        }
        else if(!empty(self::$_iniApp) && !is_string(self::$_iniApp)){
        	echo "Format error: appflag(".self::$_iniApp.") is not string!\n";
        	exit('');
        }
    
        if(empty(self::$_host)) self::$_host = self::_getEnv($_SERVER, self::C_ENV_HOST);

        if(empty(self::$_secretId)) self::$_secretId = self::_getEnv($_SERVER, self::C_ENV_SECRETID);
        
        if(empty(self::$_secretKey)) self::$_secretKey = self::_getEnv($_SERVER, self::C_ENV_SECRETKEY);

        if (    empty(self::$_host)
            ||  empty(self::$_secretId)
            ||  empty(self::$_secretKey))
            self::_help_env();
    }
    
    private static function _getEnv($args, $key){
    
        if ( isset($args[$key]) || !empty($args[$key]))
            return $args[$key];
        return '';
    }
    
    
    private static function _help_env(){
        
        $help = '';
        $help .= "env [";
        $help .=  self::C_ENV_HOST."|";
        $help .=  self::C_ENV_SECRETID."|";
        $help .=  self::C_ENV_SECRETKEY;
        $help .= "]";
        $help .= "OR input options";
        $help .= "[";
        $help .=  self::C_CMD_HOST."|";
        $help .=  self::C_CMD_SECRETID."|";
        $help .=  self::C_CMD_SECRETKEY;
        $help .= "]";
        $help .= " is required\n";
        echo $help;
        exit('');
    }
    
    private static function _help($cloudType, $opts=array() ){
		
    	$cmd = "";
    	if(isset($opts['self']))
    	{
    		$cmdTemp = explode('/',$opts['self']);
    		$cmd = array_pop($cmdTemp);
    	}
    	echo CmdHelp::GetCmdHelps($cmd);
    	exit('');
    }

    private static function _help_listAll(){
    
        //TODO: Get From configure
        
        exit('');
    }
    
    private static function _cmd($className, $methodName, $insId = 0, $args = array(), $opts){
        $class = new ReflectionClass($className);
        $instance  = $class->newInstanceArgs(array(self::$_host, self::$_secretId, self::$_secretKey));
        $method = $class->getmethod($methodName);
        $ret = array();
        if(!empty($insId) && !empty($args)){
            $ret = $method->invoke($instance, $insId, $args);
        }
        if(!empty($insId) && empty($args)){
            $ret = $method->invoke($instance, $insId);
        }
        if(empty($insId) && !empty($args)){
            $ret = $method->invoke($instance, $args);
        }
        if(empty($insId) && empty($args)){
            $ret = $method->invoke($instance);
        }
        if(!$ret)
        {
        	$errMsg = "api return null";
        	$httpCode = 500;
        	$errorCode = 500;
        	$strlen = strlen($errMsg) + 2;
            $retStr .= str_pad('httpCode',10).str_pad('errorCode',10).str_pad('errorMessage',$strlen)."\n";
            $retStr .= str_pad($httpCode,10).str_pad($errorCode,10).str_pad($errMsg,$strlen)."\n";
			$retStr .= "\n\n";
        	return $retStr;
        }
        //echo var_export($ret,true);
        // formated output
        $retStr = '';
        $retBody = $ret->getBody();

        if ($methodName == 'GetRequestInfo' && isset($retBody['req']) && isset($retBody['rsp'])) {
            $retStr = json_encode($retBody);
            $retStr .= "\n\n";
            return $retStr;
        }


        if ($retBody['httpCode'] == 200) {
            
            if(isset($retBody['requestId'])      && (!empty($retBody['requestId']))) {
            
                $retStr .= "requestid=".$retBody['requestId']['id'];
                
            } else if ( (isset($retBody['instanceInfo'])   && (!empty($retBody['instanceInfo']))) 
                     || (isset($retBody['instances'])      && (!empty($retBody['instances']))) 
            ){
                $retStr .= "\n"; 
                $arrSort = array();
                $length = array(); 
                $compare = create_function('$a, $b', ' return (strlen($a) < strlen($b)) ? 1 : -1;');
            	foreach($retBody['instanceInfo'] as $key=>$row)
                {
                	foreach($row as $k => $val)
                	{
                		$arrSort[$k][] = $val;
                	}
                }
                for($i=0;$i<count($arrSort);$i++)
                {
                	usort($arrSort[$i], $compare);
                	$length[$i] = strlen($arrSort[$i][0]) + 5;
                }                
                foreach($retBody['instanceInfo'] as $key=>$row)
                {
                	foreach($row as $k => $val)
                	{
                		$retStr .= str_pad($val,$length[$k]);
                	}
                	$retStr .= "\n";
                }

            } else if (isset($retBody['req']) && isset($retBody['rsp']) ){
                $retStr .= str_pad('httpCode',10).str_pad('result',10)."\n";
                $retStr .= str_pad($retBody['httpCode'],10).str_pad('200 OK',10);
                //$retStr .= "httpCode = ". $retBody['httpCode'] . "\n";
            } else {
                $retStr .= "No instance found\n";
            }

        } else if ($retBody['httpCode'] == 202) {
            $retStr .= "httpCode = ". $retBody['httpCode'] . "\n";
            //nothing
        } else if ($retBody['httpCode'] >= 400) {
            $strlen = strlen($retBody['errorMessage']) + 2;
            $retStr .= str_pad('httpCode',10).str_pad('errorCode',10).str_pad('errorMessage',$strlen)."\n";
            $retStr .= str_pad($retBody['httpCode'],10).str_pad($retBody['errorCode'],10).str_pad($retBody['errorMessage'],$strlen)."\n";
        }
        else
        {
        	$errMsg = "Fatal error, please check the endpoint!\n";
        	$httpCode = 400;
        	$errorCode = 400;
        	$strlen = strlen($errMsg) + 2;
            $retStr .= str_pad('httpCode',10).str_pad('errorCode',10).str_pad('errorMessage',$strlen)."\n";
            $retStr .= str_pad($httpCode,10).str_pad($errorCode,10).str_pad($errMsg,$strlen)."\n";
			$retStr .= "\n\n";
        	return $retStr;
        }
        
        $retStr .= "\n\n";
        return $retStr;
    }

    private static function _getValue($haystack, $index)
    {
        $idxArr = explode("::", $index);
        $idxStr = '';
        foreach($idxArr as $v){
            $idxStr .= "[$v]";
        }
        eval("\$result=\$haystack$idxStr;");
        return $result;
    }
    
}
//end of class

$cloudType = 'opencloud';

$shortOpts = "hlc:i:n:";
$longOpts = array(
    'help',
	'version',
    'endpoint:',
    'secretid:',
    'secretkey:',
	'self:',
	'capacity:',
	'password:',
	'charset:',
	'number:',
    'names:',
    'instanceName:',
    'newInstanceName:',
    'newCharset:',
    'newPassword:',
    'expire:',
	'domain:',
	'ip:',
	'port:',
	'ips:',
	'ports:',
	'requestId:',
	'appflag:',
	'offset:',
	'begintime:',
	'endtime:',
	'uri:',
);
$cloudClass = array(
    'cvm'=>'ApiCvms',
    'cdb'=>'ApiCdbs',
    'cmem'=>'ApiCmems',
    'request'=>'ApiRequests',
	'domain'=>'ApiDomains',
);
$opts = getopt($shortOpts, $longOpts);
CloudCmd::loadClass($cloudType, $cloudClass);
$ret = CloudCmd::run($cloudType, $opts);
exit($ret);


