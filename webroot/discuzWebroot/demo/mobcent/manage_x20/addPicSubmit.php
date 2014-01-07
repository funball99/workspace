<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=gbk" />
	<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
	<script type="text/javascript" src="wbox/mapapi.js"></script> 
	<script type="text/javascript" src="wbox/wbox.js"></script> 
</head>
<body>
<?php 

require_once '../../source/class/class_core.php';
require_once '../../config/config_ucenter.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
 
 
	$picTopicID = isset($_POST['topicid'])?$_POST['topicid']:0;
	$picTitle = isset($_POST['title'])?$_POST['title']:'';
	$picLink = isset($_POST['imglink'])?$_POST['imglink']:'';
	$picUrl = isset($_POST['Tourl'])?$_POST['Tourl']:'';
	$flag=false;
 	 
	$tpid=$tit=$link=$linkstyle=$url=0; 
	for($i=0;$i<count($picTitle);$i++){
		if($picTopicID[$i]!=""){ /*pan duan qian liang ge bu neng wei kong*/
			if($picTitle[$i]==""){
				$tit+=1;				 
			}
			if($picLink[$i]==""){
				$link+=1;				
			}
			if(substr($picLink[$i],0,7)!="http://" || substr($picLink[$i],-4)!=".jpg"){
				$linkstyle+=1;
			}
		}else{ /*pan duan hou san ge dou bu neng wei kong*/
			$tpid+=1;
			if($picTitle[$i]==""){
				$tit+=1;
			}
			if($picLink[$i]==""){
				$link+=1;
			}
			if(substr($picLink[$i],0,7)!="http://" || substr($picLink[$i],-4)!=".jpg"){
				$linkstyle+=1;
			}
			if($picUrl[$i]==""){
				$url+=1;
			}
			if(substr($picUrl[$i],0,7)!="http://"){
				$picUrl[$i]="http://".$picUrl[$i];
			}
		} 
	}
	
	$scores=0;
	if($tit<=count($picTitle) && $tit>0){
		echo Common::get_web_unicode_charset('\u6240\u6709\u6807\u9898\u90fd\u4e0d\u80fd\u4e3a\u7a7a ').'<br>';		 
		$scores+=1;
	}
	if($link<=count($picLink) && $link>0){
		echo Common::get_web_unicode_charset('\u6240\u6709\u56fe\u7247\u94fe\u63a5\u90fd\u4e0d\u80fd\u4e3a\u7a7a ').'<br>';
		 
		$scores+=1;
	}
	if($linkstyle<=count($picLink) && $linkstyle>0){
		echo '<font color=red>'.Common::get_web_unicode_charset('\u6240\u6709\u56fe\u7247\u94fe\u63a5\u683c\u5f0f\u5fc5\u987b\u4ee5\u0068\u0074\u0074\u0070\u003a\u002f\u002f\u5f00\u5934\uff0c\u5e76\u4e14\u4ee5\u002e\u006a\u0070\u0067\u7ed3\u5c3e').'</font><br>';
	 
		$scores+=1;
	}
	if($url<=$tpid && $url>0){
		echo Common::get_web_unicode_charset('\u5e16\u5b50\u0049\u0044\u4e3a\u7a7a\u65f6\uff0c\u6307\u5411\u94fe\u63a5\u4e0d\u80fd\u4e3a\u7a7a ').'<br>';
 
		$scores+=1;
	}

	if($scores!=0){
		$flag=false; 
	}else{	
		$flag=true;
		try{	
			define('IN_MOBCENT',1);
			require_once '../tool/tool.php';
		
			$dom = new DOMDocument('1.0');
			$root = $dom ->createElement('root');
			$dom ->appendChild($root);		
				
			foreach ($picTitle as $key=>$val){ 
				$board =$dom ->createElement('Item');
				$root ->appendChild($board);
				
				$topic =$dom ->createElement('topicId');
				$board ->appendChild($topic);
				$fupText  = $dom->createTextNode($picTopicID[$key]);
				$topic ->appendChild($fupText);
				
				$fup =$dom ->createElement('title');
				$board ->appendChild($fup);
				$fupText  = $dom->createTextNode(unicode_encode($val));
				$fup ->appendChild($fupText);
		
				$fid =$dom ->createElement('imglink');
				$board ->appendChild($fid);
				$fidText  = $dom->createTextNode($picLink[$key]);
				$fid ->appendChild($fidText);
		
				$fname =$dom ->createElement('Tourl');
				$board ->appendChild($fname);
				$fnameText  = $dom->createTextNode($picUrl[$key]);
				$fname ->appendChild($fnameText);
					
			}
			
			$appxml = $dom->saveXML();		
			file_put_contents('AddPic.xml',$appxml);
		
		}catch(Exception $e){
		
		}
		
	} 
		
		
?>
<div id="flags" style="visibility:hidden"><?php echo $flag; ?></div> 
<script language="javascript" type="text/javascript"> 
	var GB2312UnicodeConverter = {
        ToUnicode: function (str) {
            return escape(str).toLocaleLowerCase().replace(/%u/gi, '\\u');
        },
        ToGB2312: function (str) {
            return unescape(str.replace(/\\u/gi, '%u'));
        }

    };
    var str1 =decodeURI('%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F');
    var str2 =decodeURI('%E6%93%8D%E4%BD%9C%E5%A4%B1%E8%B4%A5%EF%BC%8C%E8%AF%B7%E9%87%8D%E8%AF%95%EF%BC%81');
    var notice=document.getElementById('flags').innerText;
    if(notice==1){
    	alert(str1);
    }else{
    	alert(str2);
    }
	
</script>
</body>
</html>