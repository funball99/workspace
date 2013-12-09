<?php 

ob_end_clean ();
define('IN_MOBCENT',1);
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once libfile('function/forumlist');
require_once '../tool/tool.php';
require_once '../Config/public.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" href="images/module.css" />
	<link rel="stylesheet" href="images/up1.css" />
	<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
	<script type="text/javascript" src="images/check.js"></script> 
	<title></title>
	<script>
	$(function(){
		$(".cancel").click(function(){
			window.location.href='index.php';
			
		})
        $(".confirm").click(function() {
      		var title=$("#title").val();
      		var orderby=$("#orderby").val();
      		var content=$("#content").val();
      		if(title==""){		 
    			alert(decodeURI('%E6%A0%87%E9%A2%98%E4%B8%8D%E8%83%BD%E4%B8%BA%E7%A9%BA%EF%BC%81'));			
    			$("#title").focus();
    			return false;
    		}
      		if(content==""){		 
    			alert(decodeURI('%E8%AF%B7%E9%80%89%E6%8B%A9%E5%86%85%E5%AE%B9%E6%9D%A5%E6%BA%90%E6%A8%A1%E5%BC%8F%EF%BC%81'));			
    			$("#content").focus();
    			return false;
    		}
    		if(orderby==""){
    			alert(decodeURI('%E6%8E%92%E5%88%97%E5%BA%8F%E5%8F%B7%E4%B8%8D%E8%83%BD%E4%B8%BA%E7%A9%BA%EF%BC%81'));
    			$("#orderby").focus();
    			return false;
    		}
        });
    });
	 
	</script>
</head>
<body>

<form action ='moduleSubmit.php' method ='post' name="module" onsubmit="return checkdata(module);">	
	<div class="sharetck">
		
		<div class="sharetck_title">
			<span><?php echo Common::get_web_unicode_charset('\u6dfb\u52a0\u8d44\u8baf\u5206\u7c7b')?></span>
		</div>
		
		<div class="discusejian2_content_two">
			<div class="discusejian2_content_two_left">
					<span class="xinghao">*</span>
					<span class="discusejian1_content_two_span">
					<?php echo Common::get_web_unicode_charset('\u6dfb\u52a0\u6807\u9898\uff1a')?></span>
				</div>
				<div class="discusejian2_content_two_right">
					<input type="text" name='title' id='title' size='40' />
					<span style="color:#666;font-size:12px;display:block;clear:both;width:350px;margin-top:5px;">
					<?php echo Common::get_web_unicode_charset('\uff08\u8bf7\u8f93\u5165\u6807\u9898\uff0c\u6b64\u9879\u5fc5\u586b\uff09 ')?></span>
					
				</div>
	   	</div>
 
		
		<div class="discusejian2_content_two">
			<div class="discusejian2_content_two_left">
					<span class="xinghao">*</span>
					<span class="discusejian1_content_two_span"><?php echo Common::get_web_unicode_charset('\u5185\u5bb9\u6765\u6e90\uff1a')?></span>
				</div>
				<div class="discusejian2_content_two_right">
						<select name="content" id="content">
						<option value='' selected><?php echo Common::get_web_unicode_charset('\u8bf7\u9009\u62e9\u5185\u5bb9\u6765\u6e90\u6a21\u5f0f')?></option>
							<option value='manual'><?php echo Common::get_web_unicode_charset('\u624b\u52a8\u6dfb\u52a0')?></option>
							<option value='automatic'><?php echo Common::get_web_unicode_charset('\u81ea\u52a8\u6dfb\u52a0')?></option>
						</select>
						<span style="color:#666;font-size:12px;display:block;clear:both;width:350px;padding-top:10px;">
						<?php $aaas="\u63d0\u4f9b\u4e24\u79cd\u5185\u5bb9\u6765\u6e90\u6a21\u5f0f\uff1a\u624b\u52a8\u6dfb\u52a0\uff08\u624b\u5de5\u5f55\u5165\u8981\u5728\u8be5\u5206\u7c7b\u5185\u5c55\u793a\u7684\u5e16\u5b50\u0069\u0064\u6216\u8005\u6587\u7ae0\u0069\u0064\uff09\uff1b\u81ea\u52a8\u6a21\u5f0f\uff1a\uff08\u901a\u8fc7\u6dfb\u52a0\u6587\u7ae0\u5206\u7c7b\u7684\u0069\u0064\u6216\u8005\u677f\u5757\u7684\u0069\u0064\uff0c\u7a0b\u5e8f\u4f1a\u81ea\u52a8\u8bfb\u53d6\u5e76\u5b9e\u65f6\u66f4\u65b0\u8be5\u5206\u7c7b\u91cc\u9762\u7684\u5185\u5bb9\uff09";
						echo Common::get_web_unicode_charset($aaas); ?>
						</span>
				</div>
				
		</div>
		
		<div class="discusejian2_content_two" style="padding-top:20px;">
			<div class="discusejian2_content_two_left">
					<span class="xinghao">*</span>
					<span class="discusejian1_content_two_span">
					<?php echo Common::get_web_unicode_charset('\u6392\u5217\u5e8f\u53f7\uff1a')?></span>
				</div>
				<div class="discusejian2_content_two_right">
						<input type="text" name='orderby' id ='orderby'  maxlength='1' size='40'  onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
				<span style="color:#666;font-size:12px;display:block;clear:both;width:350px;margin-top:5px;">
						<?php echo Common::get_web_unicode_charset('\uff08\u8bf7\u8f93\u5165\u0030\u002d\u0039\u5176\u4e2d\u7684\u4e00\u4e2a\u6570\u5b57\uff0c\u6570\u5b57\u8d8a\u5927\u6392\u5e8f\u8d8a\u9760\u524d\uff09 ') ?>
				</span>
				</div>
	   	</div>
	   	
		<div style="width:360px;height:30px;margin-top:8px;"  class ='wBox_close'>
			<div class="botton" >
				<input class="confirm" type="submit" value=''  style='cursor: pointer'/>
				<input class="cancel" type="reset" value=''  style='cursor: pointer'/>
		</div>
		</div>
	</div>
</form>
</body>
</html>