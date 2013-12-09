<?php
ob_end_clean ();
session_start();
if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
	echo "<script>location.href='index.php';</script>";
}

require_once '../model/table/x25/topic.php';
require_once '../tool/tool.php';
$xm=new topic();
$s=file_exists('../install/AppPackPwd.xml')?join("",file('../install/AppPackPwd.xml')):array();
$result =$xm->xml_to_array($s);
 
if(isset($_POST['pwd'])){ 
	if($_POST['pwd']==$result['password'][0][0]){		
		$_SESSION["admin"] = true;
		echo "<script>location.href='index.php';</script>";
	}else{
		echo Common::get_web_unicode_charset('\u5bc6\u7801\u4e0d\u6b63\u786e\uff01');
	}
 
}

if(isset($_GET['act'])){ 
	if($_GET['act']=='out'){
		unset($_SESSION["admin"]);
		$_SESSION["admin"] = false;
	}
}



?>
<form action="login.php" name="login" method ="post">
	<?php echo Common::get_web_unicode_charset('\u8bf7\u8f93\u5165\u5bc6\u7801\uff1a'); ?>
	<input type="password" name="pwd" value=""/>
	<input type="submit" value="<?php echo Common::get_web_unicode_charset('\u786e\u5b9a'); ?>" name="ok"/>
</form>
 


