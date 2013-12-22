<?php
class CmdHelp{
	
	static function getCmdHelp($args)
	{
		$retArr = array(
			'retcode' => 0,
			'ret' => ''
		);
		$cmdOver = self::_getCmdOverview();
		if(in_array($args['cmd'] ,$cmdOver['cmdKey']))
		{
			$ret = self::_getCmdOverviewHelp($args['cmd']);
			$retArr['ret'] = $ret;
            //return $ret;
		}
		elseif(in_array($args['cmd'] ,$cmdOver['cmdArr']))
		{		
			$retArr['retcode'] = 1;
		}
		else
		{
			$retArr['ret'] = "bash: {$cmd}: command not found"; 
		}
		return $retArr;
	}
	
	protected function getCmdNoParamHelp($cmd){
		$str = "{$cmd}: You must specify one of the `-Acdtrux' options\nTry `{$cmd} --help' for more information.";
		return $str;
	}
	
	protected function getCmdErrorParamHelp($cmd)
	{
		$str = "{$cmd}: invalid option -- 0\nTry `{$cmd} --help' for more information.";
	}
	
	private function _getCmdOverview()
	{
		$baseDir = dirname(__FILE__) ;
		$confObj = new Conf();
        $commonConf = $confObj->GetConf("$baseDir/../cmdconf.ini");
		$cmdOverview = 	$commonConf['cmdOverview'];
		$keyArr = array_keys($cmdOverview);
        $arr = array();
		foreach($cmdOverview as $row)
		{
			$arr = array_merge($arr , array_keys($row));
		}
		
		$retArr = array(
			'cmdKey' => array_merge($keyArr , array('qc')),
			'cmdArr' => $arr
		);
		return $retArr;
	}
	
	private function _getCmdOverviewHelp($cmd)
	{
		$baseDir = dirname(__FILE__) ;
        $confObj = new Conf();
        $commonConf = $confObj->GetConf("$baseDir/../cmdconf.ini");
		$cmdOverview = 	$commonConf['cmdOverview'];
		$cmdArr = array();
		if(array_key_exists($cmd, $cmdOverview))
		{
			$cmdArr = $cmdOverview[$cmd];
		}
		else
		{
			if('qc' === $cmd)
			{
				$arr = array();
				foreach($cmdOverview as $row)
				{
					$arr = array_merge($arr , $row);
				}
				$cmdArr = $arr;
			}
		}
        $cmdArr = array_merge(array('Command Name'=>'Description') , $cmdArr);
		return self::_getcmdStr($cmdArr);
	}
	
	private function _getcmdStr($args)
	{
		$maxLen = self::_getArrayMaxLen($args) + 5;
		$str = "";
		if(count($args) >0)
		{
			foreach($args as $key=>$val)
			{
				$str .= "  ".str_pad($key, $maxLen).$val."\n";
			}
		}
		return $str;
	}
	
	private function _getArrayMaxLen($arr)
	{
		$maxLen = 0;
        foreach($arr as $val =>$row)
		{
			$maxLen = strlen($val) > $maxLen ? strlen($val): $maxLen;
		}
		return $maxLen;
	}
	
	public function GetCmdHelps($cmd)
	{
		$baseDir = dirname(__FILE__) ;
        $confObj = new Conf();
        $commonConf = $confObj->GetConf("$baseDir/../cmdconf.ini");
		$cmdHelf = 	$commonConf['cmdType'];
		$commonHelp = $cmdHelf['common'];
		$cmdHelp = array();
		if($cmd and isset( $cmdHelf[ $cmd ] ))
		{
			$cmdHelp = $cmdHelf[ $cmd ];
		}
		
		$cmdHelpRet = array_merge($cmdHelp, $commonHelp);
		$retStr = self::_getHelpStr($cmdHelpRet);
		return $retStr;
	}
	
	private function _getHelpStr($arr)
	{
		$str = "";
		if(count($arr) > 0)
		{
			foreach($arr as $key=>$val)
			{
				if(stristr($key, '@'))
				{
					$strArr = explode('@', $key);				
					$str .= "  ".$strArr[0] . '=' . strtoupper($strArr[1]) . "\n";
				}
				else
				{
					$str .= "  ".$key . "\n";
				}
				
				$str .= "        " . $val ."\n\n";
				//$str .= str_pad($val, 8 ," ",STR_PAD_LEFT)."\n\n";
			}
		}
		return $str;
	}
}
