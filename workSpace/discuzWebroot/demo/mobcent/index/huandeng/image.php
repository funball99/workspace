<?php   ob_end_clean ();define('IN_MOBCENT',1);require_once '../../../source/class/class_core.php';
require_once '../../../source/function/function_forum.php';
require_once '../../../config/config_ucenter.php';
require_once libfile('function/forumlist'); 
require_once '../../Config/public.php';@session_start();
if(isset($_SESSION['renxing']) && $_SESSION['renxing']==true){
 ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />	<title></title><link rel="stylesheet" href="../images/images.css" /><link rel="stylesheet" href="../images/up1.css" /><script type="text/javascript" src="../wbox/jquery1.4.2.js"></script> <script type="text/javascript" src="../images/check.js"></script><script>$(function(){	$(".cancel").click(function(){		window.location.href='../manage.php';	})    $(".confirm").click(function() {    	  		var title=$("#title").val();  		var img=$("#img").val();  		var imgUrl=$("#imgUrl").val();  		var link=$("#link").val();  		var linkUrl=$("#linkUrl").val();  		var orderby=$("#orderby").val();  		  		if(imgUrl==""){		 			alert(decodeURI('%E5%9B%BE%E7%89%87%E6%9D%A5%E6%BA%90%E4%B8%8D%E8%83%BD%E4%B8%BA%E7%A9%BA%EF%BC%81'));						$("#imgUrl").focus();			return false;		}  		if(imgUrl.slice(0,7)!="http://" && isNaN(imgUrl)){		 			alert(decodeURI('%E5%9B%BE%E7%89%87%E6%9D%A5%E6%BA%90%E8%BE%93%E5%85%A5%E4%B8%8D%E5%90%88%E6%B3%95%EF%BC%8C%E8%AF%B7%E8%BE%93%E5%85%A5%E6%96%87%E7%AB%A0ID%E6%88%96%E5%B8%96%E5%AD%90ID%E6%88%96%E5%A4%96%E9%93%BE%E5%9C%B0%E5%9D%80'));						$("#imgUrl").select();			return false;		}  		 		if(linkUrl==""){			alert(decodeURI('%E6%8C%87%E5%90%91%E9%93%BE%E6%8E%A5%E4%B8%8D%E8%83%BD%E4%B8%BA%E7%A9%BA%EF%BC%81'));			$("#linkUrl").focus();			return false;		}		if(linkUrl.slice(0,7)!="http://" && isNaN(linkUrl)){		 			alert(decodeURI('%E6%8C%87%E5%90%91%E9%93%BE%E6%8E%A5%E8%BE%93%E5%85%A5%E4%B8%8D%E5%90%88%E6%B3%95%EF%BC%8C%E8%AF%B7%E8%BE%93%E5%85%A5%E8%A6%81%E8%BD%AC%E5%90%91%E7%9A%84%E6%96%87%E7%AB%A0ID%E6%88%96%E5%B8%96%E5%AD%90ID%E6%88%96%E5%A4%96%E9%93%BE%E5%9C%B0%E5%9D%80'));						$("#linkUrl").select();			return false;		}				if(orderby==""){			alert(decodeURI('%E6%8E%92%E5%88%97%E5%BA%8F%E5%8F%B7%E4%B8%8D%E8%83%BD%E4%B8%BA%E7%A9%BA%EF%BC%81'));			$("#orderby").focus();			return false;		}	     });}); 	</script>	</head><body><form action="imageSubmit.php" method='post' name="images"  onsubmit="return checkdata(images);">	<div class="sharetck">				<div class="sharetck_title">			<span><?php echo Common::get_web_unicode_charset('\u6dfb\u52a0\u5e7b\u706f\u7247')?></span>			<input type="button" />		</div>		<div class="tanchukuang_content"> 		<div class="discusejian1_content_one" style="height:40px;">				<span class="discusejian1_content_one_span"><?php echo Common::get_web_unicode_charset('\u5e7b\u706f\u6807\u9898\uff1a')?></span>				<div class="discusejian1_content_one_two">					<input type="text" name='title' id='title'/>					<span style="color:#666"><?php echo Common::get_web_unicode_charset('\uff08\u8bf7\u8f93\u5165\u6807\u9898\uff0c\u6b64\u9879\u975e\u5fc5\u586b\uff0c\u82e5\u586b\u5199\u5219\u4f18\u5148\u663e\u793a\u8be5\u6807\u9898\uff09 ')?></span>				</div>			</div>		<div class="discusejian1_content_one" style="height:40px;">				<span class="discusejian1_content_one_span">				<span class="xinghao">*&nbsp;</span>				<?php echo Common::get_web_unicode_charset('\u56fe\u7247\u6765\u6e90\uff1a')?></span>				<div class="discusejian1_content_one_selects">					<select name="img" id="img">						<option value='aid'><?php echo Common::get_web_unicode_charset('\u6587\u7ae0\u0049\u0044').'(aid)'?></option>						<option value='tid'><?php echo Common::get_web_unicode_charset('\u5e16\u5b50\u0049\u0044').'(tid)'?></option>						<option value=''><?php echo Common::get_web_unicode_charset('\u6307\u5b9a\u0075\u0072\u006c')?></option>					</select>					<input type="text" name='imgUrl'  id='imgUrl' />					<span style="color:#666">					<?php echo Common::get_web_unicode_charset('\uff08\u8bf7\u8f93\u5165\u6587\u7ae0\u0049\u0044\u6216\u5e16\u5b50\u0049\u0044\u6216\u5916\u94fe\u5730\u5740\u4f5c\u4e3a\u56fe\u7247\u6765\u6e90\uff0c\u6b64\u9879\u5fc5\u586b\uff09 ')?></span>				</div>			</div>						<div class="discusejian1_content_two"  style="height:40px;">				<div class="discusejian1_content_two_left">					<span class="xinghao">*</span>					<span class="discusejian1_content_two_span"><?php echo Common::get_web_unicode_charset('\u6307\u5411\u94fe\u63a5\uff1a')?></span>				</div>				<div class="discusejian1_content_two_right">					<select name="link" id="link">					<option value='aid'><?php echo Common::get_web_unicode_charset('\u6587\u7ae0\u0049\u0044')?></option>					<option value='tid'><?php echo Common::get_web_unicode_charset('\u5e16\u5b50\u0049\u0044')?></option>					<option value=''><?php echo Common::get_web_unicode_charset('\u6307\u5b9a\u0075\u0072\u006c')?></option>				</select>				<input type="text" name='linkUrl'  id='linkUrl'/>				<span style="color:#666;font-size:12px;height: 20px;line-height: 20px;">				<?php echo Common::get_web_unicode_charset('\uff08\u8bf7\u8f93\u5165\u70b9\u51fb\u56fe\u7247\u540e\u8981\u8f6c\u5411\u7684\u6587\u7ae0\u3001\u5e16\u5b50\u3001\u5916\u94fe\u5730\u5740\uff0c\u6b64\u9879\u5fc5\u586b\uff09 ')?></span>				</div>			</div>			<div class="discusejian1_content_two">				<div class="discusejian1_content_two_left">					<span class="xinghao">*</span>					<span class="discusejian1_content_two_span"><?php echo Common::get_web_unicode_charset('\u6392\u5217\u5e8f\u53f7\uff1a ')?></span>				</div>				<div class="discusejian1_content_three_right">					<input type="text" name='orderby' id='orderby' maxlength='1' onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>					<span style="color:#666"><?php echo Common::get_web_unicode_charset('\uff08\u8bf7\u8f93\u5165\u0030\u002d\u0039\u5176\u4e2d\u7684\u4e00\u4e2a\u6570\u5b57\uff0c\u6570\u5b57\u8d8a\u5927\u6392\u5e8f\u8d8a\u9760\u524d\uff09 ')?></span>				</div>			</div>		</div>		<div style="width:360px;height:50px;margin-top:8px;" class ='wBox_close'>			<div class="botton">				<input class="confirm" type="submit" value=''  style='cursor: pointer'/>				<input class="cancel" type="button" value=''  style='cursor: pointer'/>		</div>		</div>	</div></form></body></html><?php }else{	echo "<script>location.href='../login/login.php';</script>";}?>