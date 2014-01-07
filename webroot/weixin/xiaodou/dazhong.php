<?php
header("content-Type: text/html; charset=utf-8");
/*
$appkey="";
$secret="";

$param = array();
     //$param["city"]='北京';
     $param["latitude"]="31.21524";
     $param["longitude"]="121.420033";
   // $param["region"]="海淀区";
    // $param["category"]="火锅";
      $param["has_coupon"]="1";
       $param["sort"]="2";
     $param["limit"]="20";
     $param["radius"]="2000";
    // $param["offset_type"]="2";
   
   //  $param["has_deal"]="1";
     $param["format"]="xml";
   
$dazhong= new Sign;
$dz = $dazhong->mySign($param,$appkey,$secret);
echo $dz;
*/
//api通信签名
class Sign{
    static function mySign($para,$key,$secret)
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = self::paraFilter($para);
    
        //对待签名参数数组排序
        $para_sort = self::argSort($para_filter);
    
        //生成签名结果
        $mysign = self::buildMysign($para_sort, $key,$secret);
    
        return $mysign;
       
    
    }
    static function paraFilter($para) {
        $para_filter = array();
        foreach($para as $key=>$value){
            if($key == "sign" || $key == "filter" || $value == "")continue;
            else    $para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }
    static function argSort($para) {
        ksort($para);
        reset($para);
        return $para;
    }
    static function buildMysign($sort_para,$key,$secret) {
        //把数组所有元素，按照“参数参数值”的模式拼接成字符串
         $prestr=$key;
        $prestr =  $prestr.self::createLinkstring($sort_para);
        //把拼接后的字符串再与安全校验码直接连接起来
        $prestr = $prestr.$secret;
        //把最终的字符串签名，获得签名结果
        //$prestr= utf8_encode( $prestr);
        $mysign = strtoupper(sha1($prestr));
       
        return $mysign;
    }
    static function createLinkstring($para) {
        $arg  = "";
        foreach ($para as $key=>$value){
            $arg .= $key.$value;
        }
      
    
        return $arg;
    }
}
?>