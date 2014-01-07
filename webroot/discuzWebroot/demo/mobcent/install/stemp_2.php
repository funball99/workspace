<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf8" />
	<title></title>
	<style type="text/css">
		#sucess{
			width:440px;
			height:340px;
			border:4px solid #B4D6F2;
			margin-top:100px;
		}
		.sucess_top{
			width:440px;
			height:86px;
			background:#EEEEEE;
			position:relative;
		}
		.discur_xzaz{
			width:440px;
			height:30px;
			background:#EEEEEE;
		}
		.discur_xzaz span{
			width:140px;
			color:#095596;
			font-weight:bold;
			font-size:12px;
			display:block;
			float:left;
			margin-left:8px;
			margin-top:8px;
		}
		.discur_xzaz a{
			display:block;
			float:right;
			width:16px;
			height:16px;
			margin-right:8px;
			margin-top:5px;
		}
		.sucess_top_jc{
			width:440px;
			height:50px;
			background:#E0EAF3;
			font-size:12px;
			font-weight:bold;
			color:#666569;
			border-bottom:2px solid #C4D6E4;
		}
		.sucess_top_jc div{
			float:left;
			margin-left:32px;
			margin-top:18px
		}
		.sucess_top_img{
			background:url(images/liuchengxian_1.png);
			width:360px;
			height:12px;
			margin-left:32px;
			position:absolute;
			top:74px;
		}
		.jiantou{
			float:left;
			margin-right:160px;
		}
		.wancheng{
			float:right;
		}
		.sucess_bottom{
			width:440px;
		}
		.sucess_bottom_one{
			background:url(images/duigou_da.png);
			background-position:left center;
			background-repeat:no-repeat;
			padding-left:23px;
			font-weight:bold;
			width:300px;
			height:18px;
			margin-left:auto;
			margin-right:auto;
			margin-top:70px;
		}
		.sucess_bottom_two{
			width:200px;
			height:18px;
			font-size:12px;
			margin-left:85px;
			margin-top:30px;
		}
		.finish{
			display:block;
			width:60px;
			height:26px;
			border:none;
			background:#005494;
			color:#ffffff;
			font-size:12px;
			float:right;
			margin-right:10px;
			margin-top:70px;
		}
	</style>
</head>
<body style="margin:0px;padding:0px;" align="center">
<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
@set_magic_quotes_runtime(0);
require_once '../Config/public.php';

$arr = explode('/mobcent',$_SERVER["HTTP_REFERER"]); 
define('ROOT_PATH', dirname(__FILE__).'/../');
define('CONFIG', '../config/config_global.php');
include ROOT_PATH.CONFIG;
define('UC_DBCHARSET',$_config['db'][1]['dbcharset']);

$pwd =$_POST['pwd'];
$dom = new DOMDocument('1.0');


$App = $dom ->createElement('root');
$dom ->appendChild($App);

$Name =$dom ->createElement('password');
$App ->appendChild($Name);
$NameText  = $dom->createTextNode($pwd);
$Name ->appendChild($NameText);

$appxml = $dom->saveXML();
file_put_contents('AppPackPwd.xml',$appxml);
?>
<form action = '<?php echo $arr[0];?>'>
	<div id="sucess" style="margin-left:auto;margin-right:auto;">
		<div class="sucess_top">
			<div class="discur_xzaz">
			   <span><?php echo Common::get_web_unicode_charset('\u6469\u8baf\u0044\u0069\u0073\u0063\u0075\u007a\u0021\u4e0b\u8f7d\u5b89\u88c5');?></span>
			   <a href=""><img src="images/close.png" alt="" /></a>
		    </div>
		    <?php
			  if(file_exists('install.log') && file_get_contents('install.log')==file_get_contents('predefined.log'))
			  {
			  	echo "<script>window.location = 'index.php'</script>";
			  }else{?>
			<div class="sucess_top_jc">
				<div><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u6570\u636e\u5e93\u662f\u5426\u8fde\u63a5\u6210\u529f');?></div>
				<div><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u6587\u4ef6\u5939\u662f\u5426\u6709\u6743\u9650');?></div>
				<div><?php echo Common::get_web_unicode_charset('\u5b89\u88c5');?></div>
			</div>
			<div class="sucess_top_img">
				<div class="jiantou"><img src="images/blue_jiantou.png" alt="" /></div>
				<div class="jiantou"><img src="images/blue_jiantou.png" alt="" /></div>
				<div class="wancheng"><img src="images/green_jiantou.png" alt="" /></div>
			</div>
			<?php }?>
		</div>
		<div class="sucess_bottom">
			<div class="sucess_bottom_one"><?php echo Common::get_web_unicode_charset('\u201c\u6469\u8baf\u0044\u0069\u0073\u0063\u0075\u007a\u0021\u793e\u533a\u8f6c\u6362\u201d\u5df2\u6210\u529f\u5b89\u88c5');?></div>
			<div class="sucess_bottom_two"><?php echo Common::get_web_unicode_charset('\u5355\u51fb\u005b\u5b8c\u6210\u005d\u5173\u95ed\u6b64\u5411\u5bfc\u3002');?></div>
		    <input type="submit" value="<?php echo Common::get_web_unicode_charset('\u5b8c\u6210');?>" class="finish" id="finish" />
		</div>
	</div>
</form>
</body>
</html>
<?php 
$fopen = fopen('install.log', 'w+');
fwrite($fopen, '115',3);
?>