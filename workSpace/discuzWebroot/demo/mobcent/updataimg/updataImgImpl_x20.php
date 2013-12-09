<?php
require_once '../model/class_core.php';
require_once ('./abstractUpdataImg.php');
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../tool/tool.php';
require_once '../tool/img_do.php';
require_once '../tool/Thumbnail.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../model/table/x20/mobcentDatabase.php';
class updataImgImpl_x20 extends abstractUpdataImg {
	public function updataImg() {
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
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		$avatar_path = dirname ( __FILE__ ) . '/../../uc_server/data/avatar/cache/' . $this->get_avatar_path ( $uid );
		$avatar_file =  $this->get_avatar_file ( $uid );
		if (! file_exists( $avatar_path )) {
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
		}
		$retValue =  $avatar_path . $avatar_file [1];
		$data_post['rs'] = 1;
		$data_post['icon_url'] = 1;
		$data_post['pic_path'] = $retValue;
		
		return $data_post;
		}
		function get_avatar_path($uid) {
			$uid = abs ( intval ( $uid ) );
			$uid = sprintf ( "%09d", $uid );
			$dir1 = substr ( $uid, 0, 3 );
			$dir2 = substr ( $uid, 3, 2 );
			$dir3 = substr ( $uid, 5, 2 );
			return $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
		}
		function get_avatar_file($uid) {
			$uid = abs ( intval ( $uid ) );
			$uid = sprintf ( "%09d", $uid );
			$dir1 = substr ( $uid, 0, 3 );
			$dir2 = substr ( $uid, 3, 2 );
			$dir3 = substr ( $uid, 5, 2 );
			$typeadd = $type == 'real' ? '_real' : '';
			return array (
					substr ( $uid, - 2 ) . $typeadd . "_avatar_big.jpg",
					substr ( $uid, - 2 ) . $typeadd . "_avatar_middle.jpg",
					substr ( $uid, - 2 ) . $typeadd . "_avatar_small.jpg"
			);
		}

}

?>