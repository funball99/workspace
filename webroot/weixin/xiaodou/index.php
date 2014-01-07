<?php 
header("content-Type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . "/wechat.php");
require_once(dirname(__FILE__) . "/dazhong.php");
define("DEBUG", true);

//下面为需要配置的选项
define("TOKEN", "weisongli");
define("YOURNICK", "小蔷");//填写自定义机器人名称，不填则默认叫小豆
//在这里定义你的初次关注后的欢迎语
define("WELCOME", "初次见面,请多关照!目前支持翻译、百科、笑话、计算器、天气 、歌词、身份证、区号 手机归属.您还可以这样教我说话：问XX答XX（木有标点喔）");

//这里为大众点评的接口 申请地址:http://developer.dianping.com/
define("DZAPIKEY","0123456789" ); //大众点评SECRET
define("DZSECRET", "1234567890"); //大众点评SECRET

$w = new Wechat(TOKEN, DEBUG);

//首次验证，验证过以后可以删掉
if (isset($_GET['echostr'])) {
    $w->valid();
    exit();
}

//回复用户
$w->reply("reply_cb");

//后续必要的处理...
/* TODO */
exit();

function getxiaodou($msg)
{
$url = 'http://xiao.douqq.com/bot/chata.php?chat='.$msg;  
//$url = 'http://xiaodou.duapp.com/bot/chata.php?chat='.$msg;  
$data = file_get_contents($url);
$data = str_replace('<br />','\r\n',$data);
return $data;
}


function get_utf8_string($content) {    
	//  将一些字符转化成utf8格式   
	 $encoding = mb_detect_encoding($content, array('ASCII','UTF-8','GB2312','GBK','BIG5'));  
	   return  mb_convert_encoding($content, 'utf-8', $encoding);
	   }
	   
function reply_cb($request, $w)
{
    if ($w->get_msg_type() == "location") {
    
      
        	$dazhong= new Sign;
    	$param = array();
   
     $param["latitude"]= $request['Location_X'];
     $param["longitude"]=$request['Location_Y'];
     $param["format"]="xml";
      $param["category"]="美食";//这里可以选择返回什么类型的商品.列表请看:http://i3.dpfile.com/s/data/business_category.16ad3f3208ea2661e973e4baf5bdb1ee.json
      $param["radius"]=2000;//这里限制在多少米范围内搜索商铺
      $param["sort"]=7; //结果排序，1:默认，2:星级高优先，3:产品评价高优先，4:环境评价高优先，5:服务评价高优先，6:点评数量多优先，7:离传入经纬度坐标距离近优先 
     $dz = $dazhong->mySign($param,DZAPIKEY,DZSECRET);
     $dzurl="http://api.dianping.com/v1/business/find_businesses?appkey=".DZAPIKEY."&sign=".$dz."&longitude=". $param["longitude"]."&latitude=".$param["latitude"]."&category=美食&sort=7&radius=2000&format=xml";
    		//return 	$dzurl;
     $doc = new DOMDocument();
     $doc->load($dzurl);
     $dzshs = $doc->getElementsByTagName( "result" );
     // return  $dzshs;
     
      
       foreach( $dzshs as $dzsh )
       {
        
        $names = $dzsh->getElementsByTagName( "name" ); //取得name的标签的对象数组
        $name = $names->item(0)->nodeValue; //取得node中的值，如<name> </name>
        $name="离您最近的美食店为：".get_utf8_string($name);
        $addresss = $dzsh->getElementsByTagName( "address" ); 
        $address = $addresss->item(0)->nodeValue; 
        $address=" 地址：".get_utf8_string($address);
        $deal_titles = $dzsh->getElementsByTagName( "deal_title" ); 
        $deal_title = " 活动:".$deal_titles->item(0)->nodeValue; 
        $deal_title=get_utf8_string( $deal_title);
        $coupon_urls = $dzsh->getElementsByTagName( "coupon_url" );
        $coupon_url = $coupon_urls->item(0)->nodeValue; 
        $coupon_url=" 店铺详情：".get_utf8_string($coupon_url);
    
      }
      if($deal_title){  //这两项默认屏蔽，可开启
      	$deal_title="";
      }
      if($coupon_url){
      	$coupon_url="";
      }
 
      	
       $shop = $name.$address. $deal_title.$coupon_url;
       
      
       
        return $shop;
    }
    else if ($w->get_msg_type() == "image") { //echo back url
        $PicUrl = $request['PicUrl'];
        return "咦,我也有这张照片：" . $PicUrl;
    }else if ($w->get_msg_type() == "voice") {//用户发语音时回复语音或音乐
    
      return array(
            "title" =>  "你好",
            "description" =>  "亲爱的主人",           
            "murl" =>  "http://weixen-file.stor.sinaapp.com/b/xiaojo.mp3",
		         	"hqurl" =>  "http://weixen-file.stor.sinaapp.com/b/xiaojo.mp3",
        );
    }
    //else: Text

    $content = trim($request['Content']);
   	$firsttime = $content;
  

    if ($content !== "test") //发纯文本
    {
        //$w->set_funcflag(); //如果有必要的话，加星标，方便在web处理
        if(!empty($content)){
        	if(preg_match("/\/::\-O|\/:xx|\/:\-\-b|\/::X|\/:no|\/::~|\/::@|\/::\(|\/::Q|\/::T|\/::d|\/::!|\/::L|\/::\-S|\/:,@@|\/::8|\/:,@!|\/:!!!|\/:dig|\/:pd|\/:pig|\/:fade|\/:break|\/:li|\/:bome|\/:kn|\/:shit/",$content)){

        $content = "不开心";

         }
         if(preg_match("/\/::\)/",$content)){

        $content = "微笑";

        }
        if(preg_match("/\/:weak|\/:<@|\/:@>|\/:wipe|\/:@@|\/:bad|\/:shake/",$content)){

        $content = "不爽";

        }
        if(preg_match("/\/:moon|\/::Z|\/:\|\-\)|\/:bye|\/:beer|\/:\<W\>|\/::g/",$content)){

        $content = "再见";

        }
        if(preg_match("/\/:eat|\/:coffee/",$content)){

         $content = "吃饭";

        }
        if(preg_match("/\/:sun|\/:hiphot|\/:footb|\/:oo|\/:basketb|\/:jump|\/:circle|\/:skip|\/:<&|\/:&>/",$content)){

         $content = "运动";

        }
        if(preg_match("/\/:,@f|\/:heart|\/:showlove|\/:cake|\/:gift|\/:strong|\/::>|\/:gift|\/::B|\/:handclap|\/::\*|\/:rose|\/:kiss|\/:<L>|\/:love|\/:ok|\/:lvu|\/:jj|\/:@\)|\/:share|\/:hug/",$content)){

         $content = "喜欢";

        }
        if(preg_match("/\/::\+|\/:,@o|\/:X-\)|\/:v|\/:turn|\/:oY|\/:ladybug|\/:,@x|\/::,@|\/:8-\)|\/:#-0|\/:kotow|\/:\<O\>|\/:@x|\/:8\*|\/:P-\(|\/:>-\||\/:B-\)|\/:&-\(|\/:\?|\/::\||\/::<|\/::$|\/::-'\(|\/::-\||\/::P|\/::D|\/::O|\/:,@-D|\/:,@P/",$content)){

        $content = "夸张";

        }
          if($firsttime== "Hello2BizUser") { //这里是初次加入的欢迎语
           $content = WELCOME;
          }else{
           $to = $request['ToUserName'];
           $from = $request['FromUserName'];
          
          $content = urldecode(getxiaodou($content));
		  }
		  if(YOURNICK){
		  $content = str_replace('小豆',YOURNICK,$content);
		  }
                  
		  if($content=="")
		  {
		    $content = "我不懂唉~你教我?使用问...答...句式(木有标点)";
		  }
    if(strstr($content,'murl')){//音乐
    $a=array();
    foreach (explode('#',$content) as $content){
    list($k,$v)=explode('|',$content);
    $a[$k]=$v;
    }
     $content = $a;
    }              
    elseif(strstr($content,'pic')){//多图文回复
    $a=array();
    $b=array();
    $c=array();
    $n=0;
    foreach (explode('@',$content) as $b[$n]){
    foreach (explode('#',$b[$n]) as $content){
    list($k,$v)=explode('|',$content);
    $a[$k]=$v;
    }
     $c[$n] = $a;
     $n++;
    }
    $content = $c;
    }
    
            return  $content;
          }
        else
            return "请说点什么...";
    }
    else //发图文消息
    {
       
   
    }
}

?>
