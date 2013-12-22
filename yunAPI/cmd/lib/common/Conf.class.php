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


class Conf
{
	/**
	 * 加载配置文件
	 * 	-- 加载一个配置文件
	 */
	public static function LoadFile($file, $process_sections = true)
	{
		$conf = array();
		if ( is_file($file) )
		{
			$conf = parse_ini_file($file ,$process_sections);
		}
		
		return $conf;
	}

    public static function Get($file, $section = "", $key = "") {
        
        $conf = $this->loadFile($file);
        
        if ( empty($section) && empty($key) ) {
            return $conf;
        } else if ( !empty($section) && empty($key)) {
            return $conf[$section];
        } else if ( !empty($section) && !empty($key)) {
            return $conf[$section][$key];
        }
    }
    
    public static function Set($file, $section, $key, $value) {
        
        $conf = $this->loadFile($file);
        
        $conf[$section][$key] = $value;
        
        return $this->save($file, $conf);
    }
    
    public static function Save($file, $conf) {
        
        $str = null;
        foreach($conf as $sk => $sv) {
        
            $str .= "[$sk]\n";
            foreach($sv as $k => $v) {
            
                $strV = is_array($v) ? json_encode($v) : $v;
                
                $str .= "$k=$strV\n";
            }
        }
        return file_put_contents($file,$str);

    }

    public function GetConf($filePath, $delimiter='.')
	{
		$_array = array();
		if(!file_exists($filePath))
		{
			return $_array;
		}
		$iniFile = parse_ini_file($filePath, true);
		foreach ($iniFile as $key => $value)
		{
			$_array[$key] = $this->analy($value, $delimiter);
		}
		return $_array;
	}
    
	private function analy($array, $delimiter='.')
    {
    	if(is_array($array))
    	{
    		$newArray = array();
	    	foreach ($array as $key => $value)
	    	{
	    		$keys = explode($delimiter, $key);
	    		$this->constructArray($newArray, $keys, $value);
	    	}
	    	return $newArray;
    	}
    	return $array;
    }
    
	private function constructArray(&$array, &$keys, &$value, $n = 0)
    {
    	if($n + 1 >= count($keys))
    	{
    		isset($keys[$n]) && $array[$keys[$n]] = $value;
    		return;
    	}
    	$this->constructArray($array[$keys[$n]], $keys, $value, $n+1);
    }
};

//end of script
