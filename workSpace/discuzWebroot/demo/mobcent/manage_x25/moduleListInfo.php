<?php                 

ob_end_clean ();
define('IN_MOBCENT',1);
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once libfile('function/forumlist');
require_once '../tool/tool.php';
require_once '../Config/public.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once '../model/table/x25/table_add_portal_module.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title></title>
	<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
	<link rel="stylesheet" href="images/up1.css" />
	<style type="text/css">
		.sharetck{
			width:440px;
			height:270px;
			border:4px solid #B7D5EF;
		}
		.sharetck_title{
			width:440px;
			height:30px;
			background:#EEEEEE;
		}
		.sharetck_title span{
			display:blcok;
			width:90px;
			height:30px;
			line-height:30px;
			color:#0071C1;
			font-size:12px;
			font-weight:bold;
			float:left;
			margin-left:10px;
		}
		.sharetck_title input{
			display:block;
			width:16px;
			height:16px;
			background:url(images/close.png);
			float:right;
			margin-top:7px;
			margin-right:10px;
			border:none;
		}
	
		.tanchukuang_content{
			width:440px;
			height:90px;
		}
		.tanchukuang_content_one,.tanchukuang_content_two{
			width:340px;
			margin-left:10px;
			margin-top:10px;
			height:45px;
		}
		.tanchukuang_content_one span,.tanchukuang_content_two span{
			display:block;
			width:60px;
			height:21px;
			line-height:21px;
			color:#666666;
			float:left;
			font-size:12px;
		}
		.tanchukuang_content_one input{
			display:block;
			width:270px;
			height:17px;
			border:1px solid #999999;
		}
		.tanchukuang_content_one p{
			display:block;
			width:280px;
			color:#666666;
			float:left;
			font-size:12px;
			margin-left:55px;
			margin-top:5px;
		}
		.tanchukuang_content_two select{
			display:block;
			width:90px;
			height:21px;
			border:1px solid #999999;
			float:left;
		}
		.tanchukuang_content_two input{
			display:block;
			width:170px;
			height:17px;
			border:1px solid #999999;
			float:left;
			margin-left:10px;
		}
		
	
		.botton{
			width:110px;
			height:20px;
			clear:both;
			padding-left:235px;
		}
		.confirm{
			display:block;
			width:48px;
			height:21px;
			background:url(images/sure11.png);
			border:none;
			color:#fff;
			border-radius:2px;
			float:left;
			
		}
		.cancel{
			display:block;
			width:48px;
			height:21px;
			background:url(images/sureno11.png);
			float:right;
			border:none;
		}
	</style>
	<script type="text/javascript" src="images/check.js"></script>
	<script>
	$(function(){
        $(".confirm").click(function() {
      		var link=$("#link").val();
      		var linkUrl=$("#linkUrl").val();
      		var automaticlink=$("#automaticlink").val();
      		var automaticlinkUrl=$("#automaticlinkUrl").val();
      		if(link==""){		 
    			alert(decodeURI('%E8%AF%B7%E9%80%89%E6%8B%A9%E5%86%85%E5%AE%B9%E6%9D%A5%E6%BA%90%EF%BC%81'));			
    			$("#link").focus();
    			return false;
    		}
    		if(linkUrl==""){
    			alert(decodeURI('%E8%AF%B7%E8%BE%93%E5%85%A5%E5%B8%96%E5%AD%90Id%E6%88%96%E6%96%87%E7%AB%A0Id'));
    			$("#linkUrl").focus();
    			return false;
    		}

    		if(automaticlink==""){		 
    			alert(decodeURI('%E8%AF%B7%E9%80%89%E6%8B%A9%E5%86%85%E5%AE%B9%E6%9D%A5%E6%BA%90%EF%BC%81'));			
    			$("#automaticlink").focus();
    			return false;
    		}
    		if(automaticlinkUrl==""){		 
    			alert(decodeURI('%E8%AF%B7%E8%BE%93%E5%85%A5%E7%A4%BE%E5%8C%BA%E6%9D%BF%E5%9D%97Id%E6%88%96%E6%96%87%E7%AB%A0%E5%88%86%E7%B1%BBId'));			
    			$("#automaticlinkUrl").focus();
    			return false;
    		}
    		 
        });
    });

	 
	</script>
	<script>
	$(".cancel").click(function(){
		location.reload();
		
		})
	$(function(){
		$("#content").change(function(){
			var content= $("#content").val();
				if(content == 'automatic'){
					document.getElementById("automatic").style.display='block';
					document.getElementById("manual").style.display='none';
					}else{
						document.getElementById("automatic").style.display='none';
						document.getElementById("manual").style.display='block';
				}
			})
		})
/*	function checkdata(form){		 
		if(form.linkUrl.value==""){
			alert(decodeURI('%E5%B8%96%E5%AD%90ID%E6%88%96%E6%8C%87%E5%90%91%E9%93%BE%E6%8E%A5%E4%B8%8D%E8%83%BD%E4%B8%BA%E7%A9%BA%EF%BC%81'));
			form.linkUrl.focus();
			return false;
		}
	}*/
	</script>
</head>
<body>
<?php $id=$_REQUEST['id'];
$data = add_portal_module::check_module_edit($id);
?>
<form action="moduleListInfoSubmit.php" method='post' name="news" onsubmit="return checkdata(news);">
	<div class="sharetck">
		<input type="hidden" id="hiddenRadioValue" value='<?php echo $id;?>' name='mid'/>

		<div class="sharetck_title">
			<span><?php echo Common::get_web_unicode_charset('\u6dfb\u52a0\u5e7b\u706f\u7247')?></span>
			<input type="button" />
		</div>
				<div class="discusejian2_content_two_right">
				
					<?php if($data[0]['content']==1)
					{
					?>	
						<div id='manual' >
						<select name="link" id="link">
							<option value=''><?php echo Common::get_web_unicode_charset('\u8bf7\u9009\u62e9\u5185\u5bb9\u6765\u6e90 ')?></option>
							<option value='aid'><?php echo Common::get_web_unicode_charset('\u6587\u7ae0\u0049\u0044').'(aid)'?></option>
							<option value='tid'><?php echo Common::get_web_unicode_charset('\u5e16\u5b50\u0049\u0044').'(tid)'?></option>
						</select>
						<input class="discusejian2_content_two_right_input" type="text" name='linkUrl' id='linkUrl'/>
				
						</div>
					<?php }else{?>
					
					<div id='automatic' >
						<select name="automaticlink" id="automaticlink">
							<option value=''><?php echo Common::get_web_unicode_charset('\u8bf7\u9009\u62e9\u5185\u5bb9\u6765\u6e90 ')?></option>
							<option value='bid'><?php echo Common::get_web_unicode_charset('\u793e\u533a\u7248\u5757').'id(fid)'?></option>
							<option value='fid'><?php echo Common::get_web_unicode_charset('\u6587\u7ae0\u5206\u7c7b').'id(aid)'?></option>
						</select>
						<input class="discusejian2_content_two_right_input" type="text" name='automaticlinkUrl' id='automaticlinkUrl'/>
						<div class="discusejian2_content_two_right_down">
							<span><?php echo Common::get_web_unicode_charset('\u5c55\u793a\u5185\u5bb9\uff1a')?></span>
							<input type="radio" value='all' name='essence' checked/>
							<span><?php echo Common::get_web_unicode_charset('\u5168\u90e8\u5c55\u793a')?></span>
							<input type="radio" value='essence' name='essence'/>
							<span><?php echo Common::get_web_unicode_charset('\u4ec5\u5c55\u793a\u7cbe\u534e')?></span>
							
						</div>
				
			</div>
		<?php }?>
				<div>
				</div>

		<div style="width:360px;height:30px;margin-top:8px;" class="wBox_close">
			<div class="botton">
				<input class="confirm" type="submit" value=''/>
				<input class="cancel" type="reset" value=''/>
		</div>
		</div>
	</div>
</form>
</body>
</html>