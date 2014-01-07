<?php 
ob_end_clean ();
require_once '../../Config/public.php';
require_once '../../public/mobcentDatabase.php';
require_once '../../../source/class/class_core.php';
require_once '../tools.php';
C::app ()->init();
@session_start();

$install_log="../../install.log";
if(!file_exists($install_log)){
	echo "<script>location.href='../install/index.php';</script>"; 
}
if(isset($_SESSION['renxing']) && $_SESSION['renxing']==true){
	echo "<script>location.href='../index.php';</script>";
}else{
?>
<form action="login_submit.php" name="login" method ="post">
<?php echo Common::get_web_unicode_charset('\u7528\u6237\u540d\uff1a');?>
<input type="text" name="uname" value=""/>
<?php echo Common::get_web_unicode_charset('\u5bc6\u0020\u7801\uff1a');?>
<input type="password" name="pwd" value=""/> 
	<input type="submit" value="<?php echo Common::get_web_unicode_charset('\u786e\u0020\u5b9a');?>" name="ok" 
	style="width:50px;height:22px;border:none;background:#005494;color:#ffffff;cursor:pointer"/>
</form>
<?php 
}
?>