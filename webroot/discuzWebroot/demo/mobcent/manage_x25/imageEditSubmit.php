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

$id = isset($_POST['imgId'])?intval($_POST['imgId']):"";
$title=$_POST['title'];
$title = unicode_encode($title);
$title = str_replace("\\","\\\\",$title);

$imgUrl = $_POST['imgUrl'];
$img = $_POST['img'];
$cidtype = $_POST['link'];
$cid = $_POST['linkUrl'];
$orderby = $_POST['orderby'];

$data=DB::query("update ".DB::table('add_portal_module')." set cid='$cid',cidtype='$cidtype',imgval='$imgUrl',imgtype='$img',title='$title',display='$orderby' where id = $id");
if(isset($data)){
	   echo '<script>alert(decodeURI("%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F"));location.href="index.php";</script>';
	}else{
	   echo '<script>location.href="index.php";</script>';
	}
?>