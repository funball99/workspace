<?php   
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
	}
}else{
	echo "<script>alert(decodeURI('%E8%AF%B7%E9%80%9A%E8%BF%87%E6%AD%A3%E7%A1%AE%E6%96%B9%E5%BC%8F%E6%8F%90%E4%BA%A4%EF%BC%81'));location.href='index.php';</script>";
	exit();
}
					$predefined=join("",file('http://www.appbyme.com/mobcentACA/file/predefined.html'));
					$rst =$xm->xml_to_array($predefined);
					$new_version = $rst[mobcent_release][0][0]; 
						mkdir('../../../data/attachment/appbyme');
					}*/
					$fopen = fopen('../../install.log', 'w+');
					fwrite($fopen, $new_version,3);