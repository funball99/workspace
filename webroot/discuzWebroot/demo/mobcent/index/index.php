<?php 
ob_end_clean ();
require_once '../../source/class/class_core.php';
require_once ('../../source/discuz_version.php');
define ( 'ROOT_PATH', dirname ( __FILE__ ) . '/../' );
define ( 'CONFIG', '../config/config_global.php' );
if (DISCUZ_VERSION == 'X2'){
	echo "<script>location.href='../manage_x20/index.php';</script>";exit;
}

require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_add_portal_module.php';
require_once '../Config/public.php';
require_once '../model/table_common_member_profile.php';
require_once '../public/mobcentDatabase.php';
require_once 'install/checkModule.php';
require_once libfile('function/forumlist');
require_once '../app/config/constant.php';

C::app ()->init();
@session_start();
$install_log="../install.log";
if(!file_exists($install_log)){
	echo "<script>location.href='install/index.php';</script>";
}

$session_file='session.txt'; 
$session_time=600; /*10 minutes*/
/*
if(!file_exists($session_file)){
	fopen($session_file, "w+");
	file_put_contents($session_file, time());
}
$lasttime = file_get_contents($session_file);
if(time()-$lasttime>$session_time){
	unlink($session_file);
	unset($_SESSION['renxing']);
}else{
	file_put_contents($session_file, time());
} 
*/
if(isset($_SESSION['renxing']) && $_SESSION['renxing']==true){
	define('IN_MOBCENT',1);
	loadforum();
	set_rssauth();
	runhooks();
			
	/*version*/
	$xm=new topic();
	$this_version = MOBCENT_VERSION;
	$this_release = MOBCENT_RELEASE;
	$predefined=join("",file('http://www.appbyme.com/mobcentACA/file/predefined.html'));
	$rst =$xm->xml_to_array($predefined);
	$last_version = $rst[mobcent_version][0][0];
	$last_release = $rst[mobcent_release][0][0];

?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>mobcent</title>
	<link rel="stylesheet" href="images/anmi2discuse.css" />
	<link rel="stylesheet" href="images/anmi2discuse.css" />
	<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
</head>
<script type="text/javascript"> 
$(function(){
	$(document).ready(function(){
		$.ajax({
			type:"GET",
			url:"../requirements/index.php?ajax=1",
			success:function(data){ 
				//var data="{rs:1}";
				var objs = eval("["+data+"]");
		        var res = objs[0].rs; 
				if(res=='0'){
					alert(decodeURI('%E7%8E%AF%E5%A2%83%E9%85%8D%E7%BD%AE%E4%B8%8D%E6%BB%A1%E8%B6%B3%E8%A6%81%E6%B1%82%EF%BC%8C%E8%AF%B7%E5%85%88%E9%85%8D%E7%BD%AE%E7%8E%AF%E5%A2%83%EF%BC%81'));
					location.href="../requirements/index.php";
				} 
			}
		});
	});

	
	$(".modify2").click(function(){
		window.location.href='login/mibao.php';
	})
    $(".modify").click(function() {
  		var oldpwd=$("#oldpwd").val();
  		var newpwd=$("#newpwd").val();
  		var renewpwd=$("#renewpwd").val();
  		
  		if(oldpwd==""){		 
			alert(decodeURI('%E8%AF%B7%E8%BE%93%E5%85%A5%E5%BD%93%E5%89%8D%E5%AF%86%E7%A0%81'));			
			$("#oldpwd").focus();
			return false;
		}
  		 
		if(newpwd==""){
			alert(decodeURI('%E6%96%B0%E5%AF%86%E7%A0%81%E4%B8%8D%E8%83%BD%E4%B8%BA%E7%A9%BA'));
			$("#newpwd").focus();
			return false;
		}

		if(renewpwd==""){
			alert(decodeURI('%E7%A1%AE%E8%AE%A4%E5%AF%86%E7%A0%81%E4%B8%8D%E8%83%BD%E4%B8%BA%E7%A9%BA'));
			$("#renewpwd").focus();
			return false;
		}

		if(newpwd != renewpwd){
			alert(decodeURI('%E6%82%A8%E8%BE%93%E5%85%A5%E7%9A%84%E4%B8%A4%E6%AC%A1%E6%96%B0%E5%AF%86%E7%A0%81%E4%B8%8D%E4%B8%80%E8%87%B4%EF%BC%81'));
			$("#newpwd").select();
			return false;
		}
	 
    });
});

function updatenotice() { 
	alert(decodeURI('%E6%9B%B4%E6%96%B0%E6%97%B6%E9%97%B4%E5%A4%A7%E7%BA%A6%E9%9C%80%E8%A6%812-3%E5%88%86%E9%92%9F%EF%BC%8C%E8%AF%B7%E8%80%90%E5%BF%83%E7%AD%89%E5%BE%85%EF%BC%81'));
	alert(decodeURI('%E8%AF%B7%E4%B8%8D%E8%A6%81%E5%88%B7%E6%96%B0%E6%88%96%E5%85%B3%E9%97%AD%E9%A1%B5%E9%9D%A2%EF%BC%8C%E5%90%A6%E5%88%99%E5%B0%86%E5%AF%BC%E8%87%B4%E8%87%B4%E5%91%BD%E9%94%99%E8%AF%AF%EF%BC%81%EF%BC%81'));
	if(confirm(decodeURI('%E6%98%AF%E5%90%A6%E7%A1%AE%E5%AE%9A%E6%9B%B4%E6%96%B0%E6%8F%92%E4%BB%B6%E5%8C%85%EF%BC%9F'))){
		return true;
	}else return false;
} 
</script>
<body style="margin:0;padding:0;">
<?php require_once 'top.php';?>
<div id="discuse" style='padding-top:70px;'>
		<div class="discuse_content">
			<div style="height:21px;">
				<span class="discuse_content_sectiontitle">
				<?php echo Common::get_web_unicode_charset('\u5f53\u524d\u4f4d\u7f6e\uff1a\u9996\u9875');?>
				</span>
			</div>
		</div>
 
	<div style='color: #646464;font-size:12px;'>
	<div id='discuz_check' >
		<div class="discuse_content" >
		<div style="height:21px;margin-bottom:10px;">
			<span class="discuse_content_title"><?php echo Common::get_web_unicode_charset('\u7cfb\u7edf\u53c2\u6570')?></span>
		</div>

<table width="800" height="82" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
  <tr>
    <td height="30" bgcolor="#FFFFFF">&nbsp;&nbsp;<?php echo Common::get_web_unicode_charset('\u60a8\u5f53\u524d\u4f7f\u7528\u7684\u7248\u672c\u4e3a\uff1a').$this_version; ?></td>
  </tr>
  <tr>
    <td height="30" bgcolor="#FFFFFF">&nbsp;&nbsp;<?php echo Common::get_web_unicode_charset('\u5f53\u524d\u7cfb\u7edf\u6700\u65b0\u7248\u672c\u4e3a\uff1a').$last_version; ?>&nbsp;&nbsp;
    <?php 
    /*if(!file_exists('../../data/attachment/appbyme')){
    	mkdir('../../data/attachment/appbyme');
    }
    $local_updatefile="../mobcent_update.php";
    if(file_exists($local_updatefile)){  
    	unlink($local_updatefile);
    }
    copy("http://www.appbyme.com/mobcentACA/file/mobcent_update.txt", $local_updatefile);*/
    if((Int)$last_release > (Int)$this_release){ ?>
		<a href="updating.php" style="color:red" onclick="return updatenotice()">
		<?php echo Common::get_web_unicode_charset('\u70b9\u51fb\u66f4\u65b0\u63d2\u4ef6\u5305'); ?>
		</a>
    <?php }?>
    </td>
  </tr>
  <tr>
    <td height="30" bgcolor="#FFFFFF">&nbsp;&nbsp;<a href="http://www.appbyme.com/mobcentACA/jsp/contentManage/app_manage.jsp" target="_blank" style="color:green"><strong><?php echo Common::get_web_unicode_charset('\u70b9\u51fb\u524d\u5f80\u5b89\u7c73\u7f51\u66f4\u65b0\u5ba2\u6237\u7aef'); ?></strong></a></td>
  </tr>
</table>
		 
		<div style="clear:both;"></div>
	</div>
	<div class="discuse_content">
		<div style="height:21px;margin-bottom:10px;">
			<span class="discuse_content_title"><?php echo Common::get_web_unicode_charset('\u4fee\u6539\u5bc6\u7801')?></span>
			<span class="discuse_content_sectionspan"><?php echo Common::get_web_unicode_charset('\uff08\u82e5\u5fd8\u8bb0\u5f53\u524d\u5bc6\u7801\uff0c\u8bf7\u5230\u7f51\u7ad9').'<font color="red">/mobcent/</font>'.Common::get_web_unicode_charset('\u76ee\u5f55\u4e0b\u5220\u9664').'<font color="red">install.log</font>'.Common::get_web_unicode_charset('\u6587\u4ef6\uff09'); ?></span>
		</div>
		
<form id="form1" name="form1" method="post" action="login/modifypwd.php">
  <table width="800" border="0" cellpadding="0" cellspacing="0" style="border:1px solid #CCCCCC">
    <tr>
      <td width="150" height="40" align="right">
      <?php echo Common::get_web_unicode_charset('\u5f53\u524d\u5bc6\u7801\uff1a'); ?>&nbsp;</td>
      <td><input type="password" name="oldpwd" id="oldpwd" /></td>
    </tr>
    <tr>
      <td height="40" align="right">
      <?php echo Common::get_web_unicode_charset('\u65b0\u0020\u5bc6\u0020\u7801\uff1a'); ?>&nbsp;</td>
      <td><input type="password" name="newpwd" id="newpwd" /></td>
    </tr>
    <tr>
      <td height="40" align="right">
      <?php echo Common::get_web_unicode_charset('\u786e\u8ba4\u5bc6\u7801\uff1a'); ?>&nbsp;</td>
      <td><input type="password" name="renewpwd" id="renewpwd" /></td>
    </tr>
    <tr>
      <td height="40" align="right">&nbsp;</td>
      <td style="padding-left: 30px">
        <input type="submit" name="Submit" class="modify" value="" style="cursor:pointer"/>&nbsp;
      </td>
    </tr>
  </table>
</form>
		
	</div>
	</div>
	
	</div>
</body>

</html>

<?php 
}else{
	echo "<script>location.href='login/login.php';</script>";
}
?>