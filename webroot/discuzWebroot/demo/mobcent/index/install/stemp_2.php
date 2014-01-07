<?php   ob_end_clean ();require_once '../../Config/public.php';require_once '../../public/mobcentDatabase.php';require_once '../../../source/class/class_core.php';require_once '../../../config/config_ucenter.php';require_once '../../model/table/x25/topic.php';require 'include/install_var.php';require_once '../tools.php';C::app ()->init(); $arr = explode('/mobcent',$_SERVER["HTTP_REFERER"]);if(isset($_GET['uname']) && isset($_GET['pwd']) && isset($_GET['repwd'])){
	$uname=$_GET['uname'];
	$pwd=md5($_GET['pwd']);
	$repwd=md5($_GET['repwd']);

	$question=$_GET['question'];
	$question = unicode_encode($question);
	$question = str_replace("\\","\\\\",$question);

	$answer=$_GET['answer']; 
	$answer = unicode_encode($answer); 
	$answer = str_replace("\\","\\\\",$answer);
	$email="";
	$time=time();	
	if($pwd != $repwd){
		echo "<script>alert(decodeURI('%E4%B8%A4%E6%AC%A1%E8%BE%93%E5%85%A5%E7%9A%84%E5%AF%86%E7%A0%81%E4%B8%8D%E4%B8%80%E8%87%B4%EF%BC%81'));history.back();</script>";
		exit();
	}	mysql_query("insert into ".DB::table('add_admin')." (id,username,password,email,question,answer,time) values ('1','".$uname."','".$pwd."','".$email."','".$question."','".$answer."','".$time."')");
}else{
	echo "<script>alert(decodeURI('%E8%AF%B7%E9%80%9A%E8%BF%87%E6%AD%A3%E7%A1%AE%E6%96%B9%E5%BC%8F%E6%8F%90%E4%BA%A4%EF%BC%81'));location.href='index.php';</script>";
	exit();
} ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />	<title></title>	<link rel="stylesheet" href="images/css2.css" /></head><body style="margin:0px;padding:0px;">	<form action = "<?php echo $arr[0].'/mobcent/index/';?>">	<div id="sucess" style="margin-left:auto;margin-right:auto;">		<div class="sucess_top">			<div class="discur_xzaz">			   <span><?php echo Common::get_web_unicode_charset('\u6469\u8baf\u0044\u0069\u0073\u0063\u0075\u007a\u0021\u4e0b\u8f7d\u5b89\u88c5');?></span>			   <a href=""><img src="images/close.png" alt="" /></a>		    </div>		    <?php			  if(file_exists('../../install.log')){			  		echo "<script>window.location = 'index.php'</script>";			  }else{					$xm=new topic();
					$predefined=join("",file('http://www.appbyme.com/mobcentACA/file/predefined.html'));
					$rst =$xm->xml_to_array($predefined);
					$new_version = $rst[mobcent_release][0][0]; 					/*if(!file_exists('../../../data/attachment/appbyme')){
						mkdir('../../../data/attachment/appbyme');
					}*/
					$fopen = fopen('../../install.log', 'w+');
					fwrite($fopen, $new_version,3);			?>			<div class="sucess_top_jc">				<div><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u6570\u636e\u5e93\u662f\u5426\u8fde\u63a5\u6210\u529f');?></div>				<div><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u6587\u4ef6\u5939\u662f\u5426\u6709\u6743\u9650');?></div>				<div><?php echo Common::get_web_unicode_charset('\u5b89\u88c5');?></div>			</div>			<div class="sucess_top_img">				<div class="jiantou"><img src="images/blue_jiantou.png" alt="" /></div>				<div class="jiantou"><img src="images/blue_jiantou.png" alt="" /></div>				<div class="wancheng"><img src="images/green_jiantou.png" alt="" /></div>			</div>			<?php }?>		</div>		<div class="sucess_bottom">			<div class="sucess_bottom_one"><?php echo Common::get_web_unicode_charset('\u201c\u6469\u8baf\u0044\u0069\u0073\u0063\u0075\u007a\u0021\u793e\u533a\u8f6c\u6362\u201d\u5df2\u6210\u529f\u5b89\u88c5');?></div>			<div class="sucess_bottom_two"><?php echo Common::get_web_unicode_charset('\u5355\u51fb\u005b\u5b8c\u6210\u005d\u5173\u95ed\u6b64\u5411\u5bfc\u3002');?></div>		    <input type="submit" value="<?php echo Common::get_web_unicode_charset('\u5b8c\u6210');?>" class="finish" id="finish" style="cursor:pointer"/>		</div>	</div></form></body></html> 