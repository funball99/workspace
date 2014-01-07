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
require_once '../model/table/x25/table_add_portal_module.php';
$link = $_POST['content']=='manual'?$_POST['link']:$_POST['automaticlink'];
$linkUrl = $_POST['content']=='manual'?$_POST['linkUrl']:$_POST['automaticlinkUrl'];
$mid =$_POST['mid'];
$data = add_portal_module::check_module_edit($mid);

$link = $data[0]['content']==1?$_POST['link']:$_POST['automaticlink'];
$linkUrl = $data[0]['content']==1?$_POST['linkUrl']:$_POST['automaticlinkUrl'];
$essence = $_POST['essence']=='essence'?1:2;


$rows = DB::query('INSERT INTO %t VALUES(id,%d,%s,%s,%s,%s,%s,%d,%d,%s,%s)',array('add_portal_module',$mid,$linkUrl,$link,'','','',0,'',time(),$essence));
if(isset($rows))
{
	echo "<script> alert(decodeURI('%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F'));history.back();</script>";
}
?>