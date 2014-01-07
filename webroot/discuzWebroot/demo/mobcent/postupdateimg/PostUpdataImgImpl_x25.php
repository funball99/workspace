<?php 
require_once ('./abstractPostUpdataImg.php');
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../source/function/function_core.php';
require '../../config/config_global.php';
require_once '../tool/tool.php';
require_once '../tool/img_do.php';
require_once '../public/mobcentDatabase.php';
require_once '../tool/Thumbnail.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
class PostUpdataImgImpl_x25 extends abstractPostUpdataImg {
	function getPostUpdataImgObj() {
		$file_path = dirname ( __FILE__ );
		$save_url = '';
		$ym = date ( "Ym" );
		$d = date ( "d" );
		$save_path_image= $file_path . '/../data/attachment/forum/mobcentSmallPreview/'.$ym . "/" . $d . "/";
		$save_path_image2= $file_path . '/../data/attachment/forum/mobcentBigPreview/'.$ym . "/" . $d . "/";
		
		/*-----------------rx---20130910-------------------*/
		$setting_list = DB::query("SELECT * FROM ".DB::table('common_setting'));
		while($setting_value = DB::fetch($setting_list)) {
			$setting[] = $setting_value;
		}
		foreach($setting as $st){
			if($st[skey]=='ftp'){
				$myval=unserialize($st[svalue]);
				$ftp_on=$myval[on];
				$ftp_user=$myval[username];
				$ftp_pwd=$myval[password];
				$ftp_attachurl=$myval[attachurl];
			}
		}
		
		if(isset($ftp_on) && $ftp_on==1){
			$save_path = $ftp_attachurl.'/forum/'.$ym . "/" . $d . "/";
			$remote=1;
			global $_config;
			$se_pwd = authcode($ftp_pwd, 'DECODE', md5($_config['security']['authkey']));//jie mi hou de password
			//print_r($se_pwd);exit; 
			$pin_path=substr($save_path, 7);
			$ftp_path="ftp://".$ftp_user.":".$se_pwd."@".$pin_path;
			//$ftp_path="ftp://webmaster@zuoxiaoshuo.com:04fc735a@w258677.s105-83.myverydz.com/testaaa/forum/201309/10";
			//echo $ftp_path;exit;
			if (!file_exists($ftp_path)){
				mkdir($ftp_path,0777,true);
			} 
			//var_dump(stat($ftp_path));exit;
		}else{
			$save_path = $file_path . '/../../data/attachment/forum/'.$ym . "/" . $d . "/";
			$remote=0;
			if (! file_exists ( $save_path )) { 
				mkdir ( $save_path, 0777, true );
			} 
		}
		
		/*-----------end------rx---20130910-------------------*/

		if (! file_exists ( $save_path_image )) {
			mkdir ( $save_path_image, 0777, true );
		}
		if (! file_exists ( $save_path_image2 )) {
			mkdir ( $save_path_image2, 0777, true );
		}
		$new_file_name = date ( "YmdHis" ) . '_' . rand ( 10000, 99999 );  
		$kzm = explode ( '.', $_GET ['filename'] );
		/*rx added in 2013-08-13*/
		$string=array_keys($kzm);
		$num = count($string);
		for($i=$num;$i>0;$i--){
			for($j=0;$j<$i-1;$j++){
				if($string[$j] < $string[$j+1]){
					$tmp = $string[$j+1];
					$string[$j+1] = $string[$j];
					$string[$j] = $tmp;
				}
			}
		}
		$str_key=$string[0];
        /*end rx*/      
		$new_file_name .= '.' . $kzm[$str_key];
		$save_url =  $ym . "/" . $d . "/".$new_file_name;
		$file_url = $save_path . $new_file_name;
		
		$info = new mobcentGetInfo();
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$_G ['uid'] = $uid =$arrAccess['user_id'];
		if(empty($_G ['uid']))
		{
			return $info -> userAccessError();
			exit();
		}
		$data = file_get_contents ( 'php://input' ) ? file_get_contents ( 'php://input' ) : ($GLOBALS ['HTTP_RAW_POST_DATA']);
		
		/*if (strlen ( $data ) < 2) {
			$data['rs'] =0;
			$data['errcode'] ='013000001';
			return $data;
			exit ();
		}*/
		
		$ip = get_client_ip ();
		$tableid = explode ( '.', $ip );
		$data_attachment = array (
				'tid' => '0',
				'pid' => '0',
				'uid' => '',
				'tableid' => $tableid [0]
		);
		$aid = C::t ( 'forum_attachment' )->insert ( $data_attachment, true );
 
		
		if(isset($ftp_on) && $ftp_on==1){
			$orginalFile = $ftp_path.$new_file_name;
		}else{
			$orginalFile = $save_path.$new_file_name;
		}

		$handle = fopen ( $orginalFile, 'w' );
		if ($handle) {  
			fwrite ( $handle, $data  );
			fclose ( $handle ); 
		} else {  
			$data['rs'] =0;
			$data['errcode'] ='01300000';
			return $data;
			exit();
		}
		$img_info = getimagesize($orginalFile);
		$data_unused = array (
				'aid' => $aid,
				'dateline' => time (),
				'filename' => $new_file_name,
				'filesize' => $img_info['bits'],
				'attachment' => $save_url,
				'isimage' => '1',
				'uid' => $uid,
				'thumb' => '0',
				'remote' => (Int)$remote,
				'width' => $img_info[0]
		);
		
		C::t ( 'forum_attachment_unused' )->insert ( $data_unused, false );
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