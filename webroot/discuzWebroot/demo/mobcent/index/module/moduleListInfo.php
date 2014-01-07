<?php  
ob_end_clean ();
define('IN_MOBCENT',1);
require_once '../../../source/class/class_core.php';
require_once '../../../source/function/function_forum.php';
require_once '../../../config/config_ucenter.php';
require_once libfile('function/forumlist');
require_once '../../model/table/x25/table_add_portal_module.php';
require_once '../tools.php';
require_once '../../Config/public.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
@session_start();
if(isset($_SESSION['renxing']) && $_SESSION['renxing']==true){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title></title>
	<script type="text/javascript" src="../wbox/jquery1.4.2.js"></script> 
	<link rel="stylesheet" href="../images/up1.css" />
	<link rel="stylesheet" href="../images/moduleListInfo.css" />
	<script type="text/javascript" src="../images/check.js"></script>
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
							<option value='aid'><?php echo Common::get_web_unicode_charset('\u6587\u7ae0\u0049\u0044')?></option>
							<option value='tid'><?php echo Common::get_web_unicode_charset('\u5e16\u5b50\u0049\u0044')?></option>
						</select>
						<input class="discusejian2_content_two_right_input" type="text" name='linkUrl'/>
				
						</div>
					<?php }else{?>
					
					<div id='automatic' >
						<select name="automaticlink" id="automaticlink">
							<option value='bid'><?php echo Common::get_web_unicode_charset('\u793e\u533a\u7248\u5757')?></option>
							<option value='fid'><?php echo Common::get_web_unicode_charset('\u6587\u7ae0\u5206\u7c7b')?></option>
						</select>
						<input class="discusejian2_content_two_right_input" type="text" name='automaticlinkUrl'/>
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

		<div style="width:360px;height:30px;margin-top:8px;text-align:right" class="wBox_close">
			<div class="botton">
				<input class="confirm" type="submit" value=''/>
				<input class="cancel" type="reset" value=''/>
		</div>
		</div>
	</div>
</form>
</body>
</html>
<?php 
}else{
	echo "<script>location.href='../login/login.php';</script>";
}
?>