<?php 

ob_end_clean ();
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../model/table_common_member_profile.php';
require_once '../../config/config_ucenter.php';
require_once '../Config/public.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile ( 'function/forumlist' );
require_once '../model/table/x25/table_add_portal_module.php';
$id=$_REQUEST['id'];
$data = add_portal_module::check_module_edit($id);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" href="images/edit.css" />
	<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
	<script type="text/javascript" src="images/check.js"></script> 
	<title></title>
	<script>
	$(function(){
		$(".cancel").click(function(){
			window.location.href='index.php';
			
			})
			
          $(".confirm").click(function() {
        	  var idval =$("#idval").val();
        		var title=$("#title").val();
        		var orderby=$("#orderby").val();
        		if(title==""){		 
        			alert(decodeURI('%E6%A0%87%E9%A2%98%E4%B8%8D%E8%83%BD%E4%B8%BA%E7%A9%BA%EF%BC%81'));			
        			$("#title").focus();
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

<form action ='moduleEditSubmit.php' method ='post' name="module" onsubmit="return checkdata(module);">
	<div class="sharetck">
		<div class="sharetck_title">
			<span><?php echo Common::get_web_unicode_charset('\u4fee\u6539\u8d44\u8baf\u5206\u7c7b')?></span>
		</div>
	 
		<input type="hidden" name='id' id='idval' value='<?php echo $data[0]['id']?>'/>
		<div class="tanchukuang_content">
			<span><?php echo Common::get_web_unicode_charset('\u6807\u9898\uff1a')?></span>
			<input type="text" name='title' id='title' value="<?php echo Common::get_web_unicode_charset($data[0]['mname']); ?>" />
		</div>

		<div class="tanchukuang_content">
			<span><?php echo Common::get_web_unicode_charset('\u6392\u5e8f\uff1a')?></span>
			<input type="text" name='orderby' id ='orderby'  maxlength='1' value='<?php echo $data[0]['display']?>'/>
		</div>
		<div style="width:360px;height:30px;margin-top:8px;" class ='wBox_close'>
			<div class="botton" >
				<input class="confirm" type="submit" value='' style='cursor: pointer'/>
				<input class="cancel" type="reset" value='' style='cursor: pointer'/>
		</div>
		</div>
	</div>
</form>
</body>
</html>