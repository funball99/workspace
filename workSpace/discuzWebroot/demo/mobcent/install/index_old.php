﻿<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
@set_magic_quotes_runtime(0);

define('IN_DISCUZ', TRUE);
define('IN_COMSENZ', TRUE);
define('ROOT_PATH', dirname(__FILE__).'/../');
define('CONFIG', '../config/config_global.php');
require_once '../Config/public.php';
include ROOT_PATH.CONFIG;
define('UC_DBCHARSET',$_config['db'][1]['dbcharset']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo UC_DBCHARSET;?>" />
	<title></title>
	<style type="text/css">
		#discur{
			width:440px;
			height:400px;
			border:4px solid #B4D6F2;
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
		.discur_welcome{
			clear:both;
			background:#E0EAF3;
			height:50px;
		}
		.discur_welcome span{
			font-weight:bold;
			font-size:14px;
			display:block;
			width:270px;
			margin-left:auto;
			margin-right:auto;
			padding-top:15px;
		}
		.discur_fw{
			width:425px;
			margin-left:auto;
			margin-right:auto;
			margin-top:10px;
		}
		.discur_xy{
			width:420px;
			height:205px;
			border:2px solid #D4E4EF;
			overflow-y: auto;
			overflow-x: hidden;
		}
		.discur_xy span{
			display:block;
			width:220px;
			margin-left:auto;
			margin-right:auto;
			margin-top:10px;
			margin-bottom:20px;
			font-weight:bold;
			font-size:15px;
			font-family:black;
			color:#666569;
		}
		.discur_xy div{
			width:410px;
			margin-left:auto;
			margin-right:auto;
			font-size:12px;
			font-family:black;
			color:#666569;
		}
		.discur_fw_wty{
			width:420px;
			margin-top:15px;
		}
		.discur_fw_tk{
			float:left;
		}
		.discur_fw_ty {
			font-size:12px;
			color:5D5D5D;
			width:176px;
			display:block;
			float:left;
			margin-top:2px;
			margin-left:4px;
		}
		.discur_fw_next{
			width:150px;
			height:20px;
			clear:both;
			padding-top:20px;
			
		}
		.xyb{
			display:block;
			width:60px;
			height:26px;
			border:none;
			background:#005494;
			color:#ffffff;
			font-size:12px;
			float:left;
		}
		.qx{
			display:block;
			width:60px;
			height:26px;
			border:1px solid #D4D4D4;
			background:#FFFFFF;
			color:#005494;
			font-size:12px;
			float:left;
			margin-left:8px;
		}
	</style>

</head>
<body style="margin:0px;padding:0px;">
	<div id="discur" style="margin-left:auto;margin-right:auto;margin-top:100px">
		<div class="discur_xzaz">
			<span><?php echo Common::get_web_unicode_charset('\u6469\u8baf\u0044\u0069\u0073\u0063\u0075\u007a\u0021\u4e0b\u8f7d\u5b89\u88c5');?></span>
			<a href=""><img src="images/close.png" alt="" /></a>
		</div>
		<div class="discur_welcome">
			<span><?php echo Common::get_web_unicode_charset('\u6b22\u8fce\u4e0b\u8f7d\u5b89\u88c5\u201c\u6469\u8baf\u0044\u0069\u0073\u0063\u0075\u007a\u0021\u793e\u533a\u8f6c\u6362\u201d');?></span>
		</div>
		<div class="discur_fw">
			<form action="stemp_1.php" style="position:relative">
			  <div class="discur_xy">
			  <?php
			  if(file_exists('install.log') && file_get_contents('install.log')==file_get_contents('predefined.log'))
			  {
			  ?>
			  		   <div>
				   			   		<p><?php echo Common::get_web_unicode_charset('\u5b89\u88c5\u9501\u5b9a\uff0c\u5df2\u7ecf\u5b89\u88c5\u8fc7\u4e86\uff0c\u5982\u679c\u60a8\u786e\u5b9a\u8981\u91cd\u65b0\u5b89\u88c5\uff0c\u8bf7\u5230\u670d\u52a1\u5668\u4e0a\u5220\u9664\u0020\u0069\u006e\u0073\u0074\u0061\u006c\u006c\u002e\u006c\u006f\u0067');?></p>
										<p><font color ='red'><?php echo Common::get_web_unicode_charset('\u60a8\u5fc5\u987b\u89e3\u51b3\u4ee5\u4e0a\u95ee\u9898\uff0c\u5b89\u88c5\u624d\u53ef\u4ee5\u7ee7\u7eed');?></font></p>
				   			   </div>
		       </div>
			   <div class="discur_fw_wty">
				<input type="hidden" name="tk" checked="checked" class="discur_fw_tk" id="discurfwtk" /><span class="discur_fw_ty"></span>
			   </div>
			   <div class="discur_fw_next" style="left:295px;position:absolute">
				  <input type="button" value="<?php echo Common::get_web_unicode_charset('\u5b8c\u6210')?>" class="finsh" id="finsh" />

			   </div>
			   <?php }else{?>
			    <span><?php echo Common::get_web_unicode_charset('\u4e2d\u6587\u7248\u6388\u6743\u534f\u8bae\u0020\u9002\u7528\u4e8e\u4e2d\u6587\u7528\u6237');?></span>
				 	  <div><?php echo Common::get_web_unicode_charset('\u6469\u8baf\u793e\u533a\u8f6c\u6362\u670d\u52a1\uff08\u4ee5\u4e0b\u7b80\u79f0\u004d\u006f\u0062\u0063\u0065\u006e\u0074\uff09\u7684\u6240\u6709\u6743\u548c\u8fd0\u8425\u6743\u5f52\u5317\u4eac\u6548\u679c\u65e0\u9650\u4f20\u5a92\u4fe1\u606f\u6280\u672f\u6709\u9650\u516c\u53f8\uff08\u4ee5\u4e0b\u7b80\u79f0\u6548\u679c\u65e0\u9650\uff09\u6240\u6709\uff0c\u5e76\u4fdd\u7559\u968f\u65f6\u53d8\u66f4\u5e73\u53f0\u63d0\u4f9b\u7684\u529f\u80fd\u548c\u670d\u52a1\u7684\u6743\u5229\u3002\u3000\u6548\u679c\u65e0\u9650\u6240\u63d0\u4f9b\u7684\u76f8\u5173\u4ea7\u54c1\u548c\u670d\u52a1\u7684\u4f7f\u7528\u8005\uff08\u4ee5\u4e0b\u7b80\u79f0\u201c\u5f00\u53d1\u8005\u201d\uff09\u5728\u4f7f\u7528\u4e4b\u524d\u5fc5\u987b\u540c\u610f\u4ee5\u4e0b\u7684\u6240\u6709\u6761\u6b3e');?><p><?php echo Common::get_web_unicode_charset('\u0031\u002e\u6240\u6709\u4f7f\u7528\u004d\u006f\u0062\u0063\u0065\u006e\u0074\u7684\u5f00\u53d1\u8005\u5fc5\u987b\u9075\u5b88\u6240\u5728\u5730\u7684\u6cd5\u5f8b\u6cd5\u89c4\uff0c\u4e25\u7981\u4f7f\u7528\u004d\u006f\u0062\u0063\u0065\u006e\u0074\u5efa\u7acb\u8fdd\u6cd5\u3001\u635f\u5bb3\u4ed6\u4eba\u5408\u6cd5\u6743\u76ca\u3001\u8fdd\u53cd\u793e\u4f1a\u9053\u5fb7\u7b49\u7c7b\u578b\u793e\u533a\uff0c\u4e00\u7ecf\u53d1\u73b0\uff0c\u7ec8\u6b62\u670d\u52a1\uff1b')?></p>
						<p><?php echo Common::get_web_unicode_charset('\u0032\u002e\u5f00\u53d1\u8005\u5982\u9700\u4f7f\u7528\u004d\u006f\u0062\u0063\u0065\u006e\u0074\u5efa\u7acb\u5546\u4e1a\u5e94\u7528\uff0c\u9700\u7ecf\u672c\u516c\u53f8\u6388\u6743\u540c\u610f\u5e76\u4ed8\u8d39\u540e\uff0c\u65b9\u53ef\u4f7f\u7528\u3002\u5426\u5219\u5c06\u8ffd\u7a76\u5176\u6cd5\u5f8b\u8d23\u4efb\uff1b ')?></p>
						<p><?php echo Common::get_web_unicode_charset('\u0033\u002e\u5f00\u53d1\u8005\u4f7f\u7528\u004d\u006f\u0062\u0063\u0065\u006e\u0074\u6240\u521b\u5efa\u7684\u5e94\u7528\u7248\u6743\u5f52\u5f00\u53d1\u8005\u6240\u6709\uff0c\u5f00\u53d1\u8005\u9700\u81ea\u884c\u7ef4\u62a4\u793e\u533a\u8a00\u8bba\u53ca\u56fe\u7247\u7b49\u4fe1\u606f\u7684\u5408\u6cd5\u6027\uff0c\u5982\u7531\u6b64\u4ea7\u751f\u7684\u6cd5\u5f8b\u8d23\u4efb\uff0c\u4e0e\u672c\u516c\u53f8\u65e0\u5173\uff1b ')?></p>
						<p><?php echo Common::get_web_unicode_charset('\u0034\u002e\u5f00\u53d1\u8005\u4e0d\u5f97\u64c5\u81ea\u66f4\u6539\u004d\u006f\u0062\u0063\u0065\u006e\u0074\u6240\u63d0\u4f9b\u7684\u5e94\u7528\u6216\u63d2\u4ef6\u4e2d\u81ea\u5e26\u7684\u6587\u5b57\u4fe1\u606f\u548c\u63a5\u53e3\u51fd\u6570\uff0c\u4e0d\u5f97\u64c5\u81ea\u53cd\u7f16\u8bd1\u5ba2\u6237\u7aef\u548c\u4fee\u6539\u5ba2\u6237\u7aef\uff0c\u5426\u5219\u672c\u516c\u53f8\u6709\u6743\u968f\u65f6\u7ec8\u6b62\u670d\u52a1\u5e76\u8ffd\u7a76\u6cd5\u5f8b\u8d23\u4efb\uff1b')?></p>
						<p><?php echo Common::get_web_unicode_charset('\u0035\u002e\u5728\u672a\u7ecf\u6388\u6743\u7684\u60c5\u51b5\u4e0b\uff0c\u4efb\u4f55\u4eba\u4e0d\u5f97\u4ee5\u4efb\u4f55\u7406\u7531\u5c06\u672c\u63d2\u4ef6\u7528\u4e8e\u9664\u548c\u004d\u006f\u0062\u0063\u0065\u006e\u0074\u5408\u4f5c\u5e94\u7528\u5f00\u53d1\u4e4b\u5916\u7684\u4efb\u4f55\u7528\u9014\uff0c\u5426\u5219\u672c\u516c\u53f8\u4fdd\u7559\u76f8\u5173\u6cd5\u5f8b\u8bc9\u8bbc\u7684\u6743\u5229\uff1b')?></p>
						<p><?php echo Common::get_web_unicode_charset('\u0036\u002e\u6548\u679c\u65e0\u9650\u62e5\u6709\u6469\u8baf\u004d\u006f\u0062\u0063\u0065\u006e\u0074\u5e73\u53f0\u7f51\u7ad9\uff08\u0077\u0077\u0077\u002e\u006d\u006f\u0062\u0063\u0065\u006e\u0074\u002e\u0063\u006f\u006d\uff09\uff0c\u5305\u62ec\u56fe\u7247\u006c\u006f\u0067\u006f\u3001\u6587\u5b57\u5546\u6807\u3001\u5185\u5bb9\u7b49\u7684\u5168\u90e8\u7248\u6743\uff0c\u62e5\u6709\u004d\u006f\u0062\u0063\u0065\u006e\u0074\u6240\u63d0\u4f9b\u7684\u63d2\u4ef6\u53ca\u670d\u52a1\u7a0b\u5e8f\u7684\u5168\u90e8\u77e5\u8bc6\u4ea7\u6743\uff0c\u672a\u7ecf\u5141\u8bb8\uff0c\u4efb\u4f55\u4e2a\u4eba\u548c\u56e2\u4f53\u4e0d\u5f97\u4ee5\u4efb\u4f55\u7406\u7531\u3001\u4efb\u4f55\u5f62\u5f0f\u4f7f\u7528\u6548\u679c\u65e0\u9650\u7684\u56fe\u7247\u3001\u6587\u5b57\u548c\u5546\u6807\u7b49\uff1b')?></p>
						<p><?php echo Common::get_web_unicode_charset('\u0037\u002e\u5728\u9002\u7528\u6cd5\u5f8b\u5141\u8bb8\u7684\u8303\u56f4\u5185\uff0c\u6548\u679c\u65e0\u9650\u4fdd\u7559\u5bf9\u672c\u534f\u8bae\u4efb\u4f55\u6761\u6b3e\u7684\u89e3\u91ca\u6743\u548c\u968f\u65f6\u53d8\u66f4\u7684\u6743\u5229\u3002\u0020\u6548\u679c\u65e0\u9650\u53ef\u80fd\u4f1a\u968f\u65f6\u6839\u636e\u9700\u8981\u4fee\u6539\u672c\u534f\u8bae\u7684\u4efb\u4e00\u6761\u6b3e\u3002\u5982\u53d1\u751f\u6b64\u7c7b\u53d8\u66f4\uff0c\u6548\u679c\u65e0\u9650\u4f1a\u63d0\u4f9b\u65b0\u7248\u672c\u7684\u6761\u6b3e\uff0c\u5f00\u53d1\u8005\u5728\u53d8\u66f4\u540e\u5bf9\u6469\u8baf\u5e73\u53f0\u670d\u52a1\u7684\u4f7f\u7528\u5c06\u89c6\u4e3a\u5df2\u5b8c\u5168\u63a5\u53d7\u53d8\u66f4\u540e\u7684\u6761\u6b3e\uff1b')?></p>
	<?php echo Common::get_web_unicode_charset('\u672c\u534f\u8bae\u53ca\u56e0\u672c\u534f\u8bae\u4ea7\u751f\u7684\u4e00\u5207\u6cd5\u5f8b\u5173\u7cfb\u53ca\u7ea0\u7eb7\uff0c\u5747\u9002\u7528\u4e2d\u534e\u4eba\u6c11\u5171\u548c\u56fd\u6cd5\u5f8b\u3002\u5f00\u53d1\u8005\u4e0e\u6548\u679c\u65e0\u9650\u5728\u6b64\u9ed8\u8ba4\u540c\u610f\u4ee5\u5317\u4eac\u6548\u679c\u65e0\u9650\u4f20\u5a92\u4fe1\u606f\u6280\u672f\u6709\u9650\u516c\u53f8\u8425\u4e1a\u6240\u5728\u5730\u6cd5\u9662\u7ba1\u8f96\u3002 ')?></div>
		       </div>
			   <div class="discur_fw_wty">
				<input type="checkbox" name="tk" checked="checked" class="discur_fw_tk" id="discurfwtk" onclick='oncheck()'/><span class="discur_fw_ty"><?php echo Common::get_web_unicode_charset('\u6211\u540c\u610f\u6469\u8baf\u0044\u0069\u0073\u0063\u0075\u007a\u0021\u670d\u52a1\u6761\u6b3e')?></span>
			   </div>
			   <div class="discur_fw_next" style="left:295px;position:absolute">
				  <input type="submit" value="<?php echo Common::get_web_unicode_charset('\u4e0b\u4e00\u6b65')?>" class="xyb" id="xyb" />
			      <input type="reset" value="<?php echo Common::get_web_unicode_charset('\u53d6\u6d88')?>" class="qx" />
			   </div>
			   <?php }?>
		   </form>
		</div>
	</div>
</body>
	<script type="text/javascript">
		
			if(document.getElementById('discurfwtk').checked)
			{
				document.getElementById('xyb').disabled =false;
			}
			else
			{
				document.getElementById('xyb').disabled =true;
			}
		function oncheck()
		{
			if(document.getElementById('discurfwtk').checked)
			{
				document.getElementById('xyb').disabled =false;
			}
			else
			{
				document.getElementById('xyb').disabled =true;
			}
		}
	</script>
</html>