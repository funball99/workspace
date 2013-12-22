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


class Https
{
	public function send($param)
	{
		$url 		= $param['url'];
		$data 		= $param['data'];
		$timeout 	= $param['timeout'];
		$method 	= $param['method'];
		$proxy	 	= $param['proxy'];
        $header	 	= $param['header'];
		$ch = null;
		
		if('POST' === strtoupper($method)){
        
			$ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER,            1);
			curl_setopt($ch, CURLOPT_POST,              1);
            curl_setopt($ch, CURLOPT_HTTPHEADER,        $header);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT,     1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,    1);
			curl_setopt($ch, CURLOPT_FORBID_REUSE,      1);
			curl_setopt($ch, CURLOPT_TIMEOUT,           $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,    false);
			if(is_string($data)){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}else{
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			}
            
		}else if('GET' === strtoupper($method)){
        
			$ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER,            1);
            curl_setopt($ch, CURLOPT_HTTPHEADER,        $header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,    1);
			curl_setopt($ch, CURLOPT_TIMEOUT,           $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,    false);
            
		}else if('DELETE' === strtoupper($method)){
        
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,     'DELETE');
            curl_setopt($ch, CURLOPT_HEADER,            1);
            curl_setopt($ch, CURLOPT_HTTPHEADER,        $header);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT,     1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,    1);
			curl_setopt($ch, CURLOPT_FORBID_REUSE,      1);
			curl_setopt($ch, CURLOPT_TIMEOUT,           $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,    false);
			if(is_string($data)){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}else{
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			}
            
		}else if('PUT' === strtoupper($method)){
        
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_PUT,               true);
            curl_setopt($ch, CURLOPT_HEADER,            1);
            curl_setopt($ch, CURLOPT_HTTPHEADER,        $header);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT,     1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,    1);
			curl_setopt($ch, CURLOPT_FORBID_REUSE,      1);
			curl_setopt($ch, CURLOPT_TIMEOUT,           $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,    false);
			if(is_string($data)){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}else{
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			}
            
		}else{
			$args = func_get_args();
			return false;
		}
			
	    if($proxy){
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
	    }
	    
        
		$ret = curl_exec($ch);
		$info = curl_getinfo($ch);
				
		$contents = array(
			'httpInfo' => array(
				'send' => $data,
				'url' => $url,
				'ret' => $ret,
				'http' => $info,  		
			)
		);
	        
	    curl_close($ch); 
	    return $ret; 
	}
}
