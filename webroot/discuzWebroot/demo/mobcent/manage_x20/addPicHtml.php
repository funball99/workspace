<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=gbk" />
	<title></title>
	<link rel="stylesheet" href="images/addPic.css" />
	<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
<script type="text/javascript">
$(function(){
	 var GB2312UnicodeConverter = {
	            ToUnicode: function (str) {
	                return escape(str).toLocaleLowerCase().replace(/%u/gi, '\\u');
	            },
	            ToGB2312: function (str) {
	                return unescape(str.replace(/\\u/gi, '%u'));
	            }

	        };
     var i=1;
        $("#copy").click(function(){
        	var rowLength = $("input#imgtitle").length;
        	if(i >4)
        	{
        		var strInfo = decodeURI('%E5%AF%B9%E4%B8%8D%E8%B5%B7%EF%BC%8C%E6%9C%80%E5%A4%9A%E5%8F%AA%E8%83%BD%E4%B8%8A%E4%BC%A05%E5%BC%A0%E5%9B%BE%E7%89%87');
            	alert(strInfo);
            }else{
            	var str ='<div class="tanchukuang_content" style="margin-top:30px">';
            		str+='<div class="tanchukuang_content_one"><span class="tanchukuang_content_span">';
            		str+= decodeURI('%E5%B8%96%E5%AD%90ID%EF%BC%9A');
            		str+= '</span><input class="tanchukuang_content_input" type="text" name ="topicid[]" id ="imgtitle"/></div>';
            		str+= '<div class="tanchukuang_content_one"><span class="tanchukuang_content_span">';
            		str+= decodeURI('%E6%A0%87%E9%A2%98%EF%BC%9A');
            		str+= '</span><input class="tanchukuang_content_input" type="text" name ="title[]" id =""/></div>';
            		str+= '<div class="tanchukuang_content_two"><span class="tanchukuang_content_span">';
            		str+= decodeURI('%E5%9B%BE%E7%89%87%E9%93%BE%E6%8E%A5%EF%BC%9A');
            		str+= '</span><input class="tanchukuang_content_input" type="text" name="imglink[]"/><span class="tanchukuang_content_two_span" style="color:red">';
            		str+= decodeURI('%E8%AF%B7%E4%BF%9D%E8%AF%81%E5%9B%BE%E7%89%87%E4%BB%A5http://%E5%BC%80%E5%A4%B4%EF%BC%8C%E5%B9%B6%E4%B8%94%E4%BB%A5.jpg%E7%BB%93%E5%B0%BE ');
            		str+= '</span><span class="tanchukuang_content_two_span">';
            		str+= decodeURI('%EF%BC%88%E5%BB%BA%E8%AE%AE%E4%BD%BF%E7%94%A8%E5%AE%BD%E9%AB%98%E6%AF%94%E4%B8%BA5:3%E7%9A%84%E5%9B%BE%E7%89%87%EF%BC%8C%E6%95%88%E6%9E%9C%E6%9B%B4%E4%BD%B3%EF%BC%89');
            		str+= '</span></div>';
            		str+= '<div class="tanchukuang_content_three"><span class="tanchukuang_content_span">';
            		str+= decodeURI('%E6%8C%87%E5%90%91%E9%93%BE%E6%8E%A5%EF%BC%9A');
            		str+= '</span><input class="tanchukuang_content_input" type="text" name="Tourl[]"  value="http://"/><span class="tanchukuang_content_two_span" style="color:red">';
    				str+= decodeURI('%E6%AD%A4%E5%A4%84%E4%B8%8D%E8%A6%81%E5%88%A0%E9%99%A4http://%E6%A0%87%E8%AF%86%EF%BC%8C%E5%90%A6%E5%88%99%E4%BC%9A%E5%87%BA%E9%94%99');
    				str+= '</span></div><div id="maindiv"></div></div>';
 
                	$("#maindiv").append(str);
                }
            i++;
	    }) 
           
           
})

 

</script>
	<style type="text/css">
		
	</style>
</head>
<?php define('IN_MOBCENT',1); 
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../Config/public.php';
?>
<body>

<div style="border:4px solid #B7D5EF;width:400px;height:auto">
		
		 
		<div class="sharetck_title" style="width:400px">
			<span><?php echo Common::get_web_unicode_charset('\u6dfb\u52a0\u5e7b\u706f\u7247') ?></span>
			<input type="button" />
		</div>
	<form action= 'addPicSubmit.php' method ='post'>
		<div class="tanchukuang_content">
			<div class="tanchukuang_content_one">
				<span class="tanchukuang_content_span"><?php echo Common::get_web_unicode_charset('\u5e16\u5b50\u0049\u0044\uff1a')?></span>
				<input class="tanchukuang_content_input" type="text" name ='topicid[]' id ='imgtitle'/>
			</div>
			<div class="tanchukuang_content_one">
				<span class="tanchukuang_content_span"><?php echo Common::get_web_unicode_charset('\u6807\u9898\uff1a')?></span>
				<input class="tanchukuang_content_input" type="text" name ='title[]' id =''/>
			</div>
			<div class="tanchukuang_content_two">
				<span class="tanchukuang_content_span"><?php echo Common::get_web_unicode_charset('\u56fe\u7247\u94fe\u63a5\uff1a')?></span>
				<input class="tanchukuang_content_input" type="text" name='imglink[]'/>
				<span class="tanchukuang_content_two_span" style="color:red"><?php echo Common::get_web_unicode_charset('\u8bf7\u4fdd\u8bc1\u56fe\u7247\u4ee5\u0068\u0074\u0074\u0070\u003a\u002f\u002f\u5f00\u5934\uff0c\u5e76\u4e14\u4ee5\u002e\u006a\u0070\u0067\u7ed3\u5c3e ')?></span>
				<span class="tanchukuang_content_two_span"><?php echo Common::get_web_unicode_charset('\uff08\u5efa\u8bae\u4f7f\u7528\u5bbd\u9ad8\u6bd4\u4e3a\u0035\u003a\u0033\u7684\u56fe\u7247\uff0c\u6548\u679c\u66f4\u4f73\uff09')?></span>
			</div>
			<div class="tanchukuang_content_three">
				<span class="tanchukuang_content_span"><?php echo Common::get_web_unicode_charset('\u6307\u5411\u94fe\u63a5\uff1a')?></span>
				<input class="tanchukuang_content_input" type="text" name='Tourl[]' value="http://"/>
				<span class="tanchukuang_content_two_span" style="color:red"><?php echo Common::get_web_unicode_charset('\u6b64\u5904\u4e0d\u8981\u5220\u9664\u0068\u0074\u0074\u0070\u003a\u002f\u002f\u6807\u8bc6\uff0c\u5426\u5219\u4f1a\u51fa\u9519  ')?></span>
				 
			</div>
				<div id='maindiv' style="padding-top:20px;padding-bottom:20px;">
		</div>
			<a id='copy'><?php echo Common::get_web_unicode_charset('\u002b')?>&nbsp;<?php echo Common::get_web_unicode_charset('\u518d\u6dfb\u52a0\u4e00\u5f20')?></a>
		</div>
	
		<div style="width:360px;height:30px;margin-top:20px;">
			<div id="anniu" class ='wBox_close'>
				<input class="confirm" type="submit" value =''/>
				<input class="cancel" type="reset" value=''/>
			</div>
		</div>
</form></div>
</body>
</html>