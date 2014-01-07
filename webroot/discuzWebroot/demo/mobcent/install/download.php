<?php
require_once '../tool/tool.php';
if(!empty($_POST['iosUrl']) && !empty($_POST['androidUrl']))
{
	$androidurl = downloadurl($_POST['androidUrl'],'../data/android/');
	$iosurl = downloadurl($_POST['iosUrl'],'../data/ios/');
	if($androidurl && $iosurl)
	{
			$data['rs'] =1;
			echo echo_json($data);
	}
	else{
		$data['rs'] =0;
		echo echo_json($data);
	}
}
else {
	$data['rs'] =0;
	echo echo_json($data);
}
function mkrdirUrl($dir){
	return is_dir($dir) or (mkrdirUrl(dirname($dir)) and mkdir($dir, 0777,true));
}
function downloadurl($android,$destination_folder)
{
	set_time_limit (24 * 60 * 60);
	mkrdirUrl($destination_folder);
	$arr = get_headers($android,true);
	$path=pathinfo( $android);
	$newfname = $destination_folder . 'mobcent.'.$path['extension'];
	$file = @fopen ($android, "rb");
	if ($file) {
		$newf = @fopen ($newfname, "wb");
		if ($newf)
			try
			{
				while(!feof($file)) {
					fwrite($newf, fread($file,$arr['Content-Length']),$arr['Content-Length']);
				};
				return  true;
			}
			catch (Exception $e)
			{
				return  false;
			}
	
	}
	if ($file) {
		fclose($file);
	}
	if ($newf) {
		fclose($newf);
	}
}


?>
