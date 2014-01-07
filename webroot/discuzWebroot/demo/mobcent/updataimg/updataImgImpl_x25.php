<?php
define('UC_API', true);
define('IN_MOBCENT',1);
require_once '../../source/class/class_core.php';
require_once '../../uc_client/client.php';
require_once '../../uc_client/model/base.php';
require_once '../../uc_client/model/user.php';
require_once '../tool/tool.php';
require_once '../../config/config_ucenter.php';
require_once DISCUZ_ROOT.'./uc_client/client.php';
require_once DISCUZ_ROOT.'./source/function/function_member.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once '../public/mobcentDatabase.php';
require_once ('./abstractUpdataImg.php');
require_once '../tool/Thumbnail.php';

class updataImgImpl_x25 extends abstractUpdataImg {
	function updataImg() {
	
		$info = new mobcentGetInfo ();
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
		$uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return $info -> userAccessError();
			exit();
		}
		
$avatar_path = dirname ( __FILE__ ) . '/../../uc_server/data/avatar/cache/' .get_avatar_path ( $uid );
$avatar_file = get_avatar_file ( $uid );
if (! is_dir ( $avatar_path )) {
	mkdir ( $avatar_path, 0777, true );
}
$data = file_get_contents ( 'php://input' ) ? file_get_contents ( 'php://input' ) : ($GLOBALS ['HTTP_RAW_POST_DATA']);

foreach ( $avatar_file as $k => $new_file_name ) {
	$file = $avatar_path . $new_file_name;
	if (strlen ( $data ) > 2) {
		if (is_file ( $file ))
			unlink ( $file );
		$handle = fopen ( $file, 'w+' );
		fwrite ( $handle, $data );
		fclose ( $handle );
	}
	
	
	$res = new Thumbnail($file,$new_file_name);
	$fiel_name_array = explode ( '_', $new_file_name );
	$img_name_type = end ( $fiel_name_array );
	switch ($img_name_type) {
		case "big.jpg" :
			$res -> zoomcutPic($file, $avatar_path, $new_file_name, 150);
			if ($res) {
				$res = true;
			} else {
				$res = false;
			}
	
			break;
		case "middle.jpg" :
			$res -> zoomcutPic($file, $avatar_path, $new_file_name, 120);
			if ($res) {
				$res = true;
			} else {
				$res = false;
			}
			break;
		case "small.jpg" :
			$res -> zoomcutPic($file, $avatar_path, $new_file_name, 48);
			if ($res) {
				$res = true;
			} else {
				$res = false;
			}
			break;
	}
	if($res == false)
	{
		echo '{"rs":0,"errcode":"01300000"}';
		exit ();
	}
	$file_url = $avatar_path . $new_file_name;
}

$serverPath =  'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$retValue =  $serverPath.'/../../../uc_server/data/avatar/cache/000/00/00/'. $avatar_file [1];
	//$retValue =  $avatar_path . $avatar_file [1];
	
	 $obj['rs'] = 1;
	  $obj['icon_url'] = '';
	   $obj['pic_path'] = $retValue;
	 return $obj;


		}

}

?>