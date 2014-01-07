<?php
require_once '../model/class_core.php';
require_once ('./abstractPostUpdataImg.php');
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../tool/tool.php';
require_once '../tool/img_do.php';
include_once '../Config/public.php';
require_once '../tool/Thumbnail.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();

class PostUpdataImgImpl_x20 extends abstractPostUpdataImg {
	public function getPostUpdataImgObj() {
		$file_path = dirname ( __FILE__ );
		$save_path = $file_path . '/../../data/attachment/forum/';
		
		$ym = date ( "Ym" );
		$d = date ( "d" );
		$save_path_image= $file_path . '/../data/attachment/forum/mobcentSmallPreview/'.$ym . "/" . $d . "/";
		$save_path_image2= $file_path . '/../data/attachment/forum/mobcentBigPreview/'.$ym . "/" . $d . "/";
		$save_path .= $ym . "/" . $d . "/";
		
		if (! file_exists ( $save_path )) {
			mkdir ( $save_path, 0777, true );
		}
		if (! file_exists ( $save_path_image )) {
			mkdir ( $save_path_image, 0777, true );
		}
		if (! file_exists ( $save_path_image2 )) {
			mkdir ( $save_path_image2, 0777, true );
		}
		$new_file_name = date ( "YmdHis" ) . '_' . rand ( 10000, 99999 );  
		$kzm = explode ( '.', $_GET ['filename'] );
		$new_file_name .= '.' . $kzm [1];
		$file_url = $save_path . $new_file_name;
		$save_url =  $ym . "/" . $d . "/".$new_file_name;
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$arrAccess = C::t('common_member')->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $arrAccess['user_id'];
		if(empty($uid))
		{
			return C::t('common_member') -> userAccessError();
			exit();
		}
		$row=array();
		$data = file_get_contents ( 'php://input' ) ? file_get_contents ( 'php://input' ) : ($GLOBALS ['HTTP_RAW_POST_DATA']);
		
		if (strlen ( $data ) < 2) {
			$row['rs'] =0;
			$row['errcode'] ='013000001';
			return $row;
			exit ();
		}
		
		
		$ip = get_client_ip ();
		$tableid = explode ( '.', $ip );
		$data_attachment = array (
				'tid' => '0',
				'pid' => '0',
				'uid' => '',
				'tableid' => $tableid [0]
		);

		$aid =DB::insert('forum_attachment', $data_attachment, true);
		
		
		$data_unused = array (
				'aid' => $aid,
				'dateline' => time (),
				'filename' => '',
				'filesize' => '',
				'attachment' =>  $save_url,
				'isimage' => '1',
				'uid' => $uid,
				'thumb' => '0',
				'remote' => '0',
				'width' => 320
		);
		DB::insert('forum_attachment_unused', $data_unused, false, true);
		
		
		
		$handle = fopen ( $file_url, 'w' );
		if ($handle) {
			fwrite ( $handle, $data  );
			fclose ( $handle );
		} else {
			$data['rs'] =0;
			$data['errcode'] ='01300000';
			return $data;
			exit();
		}
		
		
		$img_name = $save_path_image . $new_file_name;  
		try{
			if(file_exists($img_name))
			{
				$filename =  $img_name; 
			}
			else
			{
			
				$pic = new Thumbnail($file_url);
			
				if($pic->zoomcutPic($file_url,$save_path_image ,$new_file_name ,160)&&$pic->zoomcutPic($file_url,$save_path_image2 ,$new_file_name ,480))
				{
					$filename = $img_name;
				}
			}
			$row['rs'] =1;
			$row['pic_path'] ="$file_url";
			$row['aid'] =(Int)"$aid";
			return $row;
		}catch (Exception $e)
		{
			$row['rs'] =0;
			$row['errcode'] ='013000003';
			return $row;
		}

		
		}

}

?>