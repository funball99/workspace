
<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
<script type="text/javascript" src="images/check.js"></script> 
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
	$id = isset($_POST['id'])?(Int)$_POST['id']:'';
	$title=$_POST['title'];
	$title = unicode_encode($title);	 
	$title = str_replace("\\","\\\\",$title);
	
	$orderby = $_POST['orderby'];
	$data=DB::query("update ".DB::table('add_module')." set mname='$title',display=$orderby where id = $id");
	if(isset($data)){
	   echo '<script>alert(decodeURI("%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F"));location.href="index.php";</script>';
	}else{
	   echo '<script>location.href="index.php";</script>';
	}
	
?>
