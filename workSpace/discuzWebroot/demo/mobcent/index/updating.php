<?php 
ob_end_clean (); 
require_once '../tool/tool.php';
@session_start();
if(isset($_SESSION['renxing']) && $_SESSION['renxing']==true){
$local_mobcentzip="../../mobcent.zip";
if(file_exists($local_mobcentzip)){
	unlink($local_mobcentzip);
}
/*
if(!file_exists('../../data/attachment/appbyme')){
	mkdir('../../data/attachment/appbyme');
}*/
copy("http://www.appbyme.com/mobcentACA/file/mobcent.zip", "../mobcent.zip");

?>

<?php echo Common::get_web_unicode_charset('\u8bf7\u7a0d\u5019\uff0c\u7cfb\u7edf\u5c06\u5728'); ?>
<span id="tiao">3</span><a href="javascript:countDown"></a>
<?php echo Common::get_web_unicode_charset('\u79d2\u540e\u81ea\u52a8\u8df3\u8f6c'); ?>....
<meta http-equiv=refresh content=3;url='../mobcent_update.php'>
<script language="javascript" type="">
function countDown(secs){
  tiao.innerText=secs;
  if(--secs>0)
   setTimeout("countDown("+secs+")",1000);
  }
  countDown(3);
</script>
<?php 
}else{
	echo "<script>location.href='login/login.php';</script>";
}
?>