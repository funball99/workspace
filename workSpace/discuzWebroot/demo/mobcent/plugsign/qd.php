<?php
require_once './abstractPlugSign.php';
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/class/table/table_forum_thread.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/constants.php';
require_once '../model/table_forum_thread.php';
require_once '../helper/helper_notification.php';
require_once '../model/table_surround_user.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'class/credit' );
require_once libfile ( 'function/post' );
require_once libfile ( 'function/forum' );
require_once '../model/table/x25/table_common_member.php';
require_once '../model/table/x25/topic.php';

class PlugSignImpl_x25 extends abstractPlugSign { 
	public function getPlugSignObj() { 
		function isdate($str,$format="Y-m-d"){
			$strArr = explode("-",$str);
			if(empty($strArr)){
				return false;
			}
			foreach($strArr as $val){
				if(strlen($val)<2){
					$val="0".$val;
				}
				$newArr[]=$val;
			}
			$str =implode("-",$newArr);
			$unixTime=strtotime($str);
			$checkDate= date($format,$unixTime);
			if($checkDate==$str){
				return true;
			}else{
				return false;
			}
		}
		
		$rPostion = $_GET['r'] ? $_GET['r']:0;  
		$longitude =$_GET['longitude'];	
		$latitude =	$_GET['latitude'];	
		$location	=	echo_urldecode($_GET['location']);
		$aid = $_REQUEST ['aid']; 
		$aid_Img=explode(',',$aid);
		$_G ['fid'] = $_GET ['boardId'];
		require_once '../public/mobcentDatabase.php';
		$info = new mobcentGetInfo ();
		$modnewposts = $info ->getBoard($_G ['fid']);
		$readperm = 0;
		$price = 0;
		$typeid = 0;
		$sortid = 0;
		$displayorder = $modnewposts['modnewposts'] > 0?-2:0;
		$digest = 0;
		$special = 0;
		$attachment = 0;
		$moderated = 0;
		$isgroup = 0;
		$replycredit = 0;
		$closed = 0;
		$publishdate = time ();
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
		$group = $info->rank_check_allow($accessSecret,$accessToken,$qquser);
		if(!$group['allowvisit'])
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '01110001';
			return $data_post;
			exit();
		}
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$ruid = $_G ['uid'] =$arrAccess['user_id'];
		$space = $info->getUserInfo ( intval ( $ruid ) );
		if(empty($_G ['uid']))
		{
			return $info -> userAccessError();
			exit();
		}
		$author = $space ['username'];
		$_G ['username'] = $lastposter = $author;
		$_G = array_merge ( $_G, $space );
	}
}

$aasd=new PlugSignImpl_x25();
$aasd-> getPlugSignObj();

if($_GET['modify']){
	DB::query("UPDATE ".DB::table('dsu_paulsign')." SET time='".(time()-86400)."' WHERE uid='".$_GET['modify']."'");
	echo "<script>alert('modify ok');location.href='qd.php';</script>";
}

if($_GET['delete']){
	DB::query("DELETE FROM ".DB::table('dsu_paulsign')." WHERE uid='".$_GET['delete']."'");
	echo "<script>alert('delete ok');location.href='qd.php';</script>";
}

?>

<input type="hidden" id="uid" value="<?php echo $_G ['uid']?>">
<input type="button"  value="delete" onclick=shanchu() >
<input type="button"  value="xiugai" onclick=xiugai() >

<script>
function xiugai(){
	var uid=document.getElementById("uid").value;
	location.href="qd.php?modify="+uid;
}

function shanchu(){
	var uid=document.getElementById("uid").value;
	location.href="qd.php?delete="+uid;
}
</script>


