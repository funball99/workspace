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


class Signature 
{

    /**
       @input = array(
            'secretId'  => string,
            'secretKey' => string,
            'url'       => string,
            'method'    => string,
            'body'      => string,
        )
        
       @output = array(
           'sig'    =>string,
           'header' =>array(
                'x-txc-cloud-secretid'      => $secret['id'],
                'x-txc-cloud-nonce'         => int,
                'x-txc-cloud-timestamp'     => int,
                'x-txc-cloud-signature'     => $sig,
           ),
        )
     **/
     
    public static function GET($secretId, $secretKey, $url, $method, $body) {

        $timestamp = time();
        $nonce = rand(0,(1<<32)-1);
        
        $str2Sig = null;
        $arr2Sig = array(
            'body'      =>"body=".(string)$body,
            'method'    =>"method=".strtoupper($method),
            'url'       =>"uri=".(string)$url,
            'x-txc-cloud-secretid'      =>"x-txc-cloud-secretid=".(string)$secretId,
            'x-txc-cloud-nonce'         =>"x-txc-cloud-nonce=".$nonce,
            'x-txc-cloud-timestamp'     =>"x-txc-cloud-timestamp=".$timestamp,
        );
        $str2Sig = implode("&", $arr2Sig);

        //Calculate HMAC-SHA1 according to RFC2104
        $sig = base64_encode(hash_hmac("sha1", $str2Sig, $secretKey, true));
        
        $output = array(
            'sig'    =>$sig,
            'header' =>array(
                "Content-type: application/json; charset=utf-8",
                "x-txc-cloud-secretid:$secretId",
                "x-txc-cloud-nonce:$nonce",
                "x-txc-cloud-timestamp:$timestamp",
                "x-txc-cloud-signature:$sig",
            ),
        );
        
        return $output;
    }

}


//end of script




















