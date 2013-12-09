 <?php
 
	 try{ 
		
		define('IN_MOBCENT',1);
		require_once '../../source/class/class_core.php';
		require_once '../Config/public.php';
		require_once '../tool/tool.php';
		
		$board_arr=$_POST['fid'];  
		$class_arr=$_POST['classfid'];
		$version = 2; 
		$dom = new DOMDocument('1.0');
		$root = $dom ->createElement('root');
		$dom ->appendChild($root);
		
		$ver =$dom ->createElement('version');
		$root ->appendChild($ver);
		$verText  = $dom->createTextNode($version);
		$ver ->appendChild($verText);
		
		$board =$dom ->createElement('board');
		$root ->appendChild($board);
			 
		foreach ($board_arr as $k){
			
			$f=explode("@",$k);
			$f1 =$f[0]; /*fid*/
			$f2 =$f[1]; /*fname*/
		 	$f3 =$f[2]; /*fup*/
		 	
			$fup =$dom ->createElement('fup');
			$board ->appendChild($fup);
			$fupText  = $dom->createTextNode($f3);
			$fup ->appendChild($fupText);
			
			$fid =$dom ->createElement('fid');
			$board ->appendChild($fid);
			$fidText  = $dom->createTextNode($f1);
			$fid ->appendChild($fidText);
		
			$fname =$dom ->createElement('fname');
			$board ->appendChild($fname);
			$fnameText  = $dom->createTextNode($f2);
			$fname ->appendChild($fnameText);
			 
		}
		$classFid =$dom ->createElement('classFid');
		$root ->appendChild($classFid);
		foreach ($class_arr as $k){
			$classData =$dom ->createElement('classItem');
			$classFid ->appendChild($classData);
			$classText  = $dom->createTextNode($k);
			$classData ->appendChild($classText);
		}
		
		$login =$dom ->createElement('login');
		$root ->appendChild($login); 
		$loginText  = $dom->createTextNode($_POST['login_count']);
		$login ->appendChild($loginText); 
		
		$allowReg =$dom ->createElement('allowReg');
		$root ->appendChild($allowReg);
		$allowRegText  = $dom->createTextNode($_POST['isreg']);
		$allowReg ->appendChild($allowRegText);
		
		$weiboshow =$dom ->createElement('weiboshow');
		$root ->appendChild($weiboshow);
		$weiboshowText  = $dom->createTextNode($_POST['weiboshow']);
		$weiboshow ->appendChild($weiboshowText);
		
		$qqshow =$dom ->createElement('qqshow');
		$root ->appendChild($qqshow);
		$qqshowText  = $dom->createTextNode($_POST['qqshow']);
		$qqshow ->appendChild($qqshowText);
		
		if(isset($_POST['classhd'])){
			$hd =$dom ->createElement('classHd');
			$root ->appendChild($hd);
			$hdText  = $dom->createTextNode($_POST['classhd']);
			$hd ->appendChild($hdText);
		}
		if(isset($_POST['classtz'])){
			$topic =$dom ->createElement('classtz');
			$root ->appendChild($topic);
			$TopicText  = $dom->createTextNode($_POST['classtz']);
			$topic ->appendChild($TopicText);
		}
		if(isset($_POST['register_count'])){
			$reg =$dom ->createElement('register');
			$root ->appendChild($reg); 
			$regText  = $dom->createTextNode($_POST['register_count']);
			$reg ->appendChild($regText); 
		}
		
		if(isset($_POST['wbkey'])){
			$qq =$dom ->createElement('wbkey');
			$root ->appendChild($qq);
			$qqText  = $dom->createTextNode(unicode_encode($_POST['wbkey']));
			$qq ->appendChild($qqText);
		}
		
		if(isset($_POST['secret'])){
			$secret =$dom ->createElement('secret');
			$root ->appendChild($secret);
			$secretText  = $dom->createTextNode(unicode_encode($_POST['secret']));
			$secret ->appendChild($secretText);
		}
		
		$appxml = $dom->saveXML();	 
		if(!file_exists('../manage')){
			mkdir('../manage');
		}
		file_put_contents('../manage/App.xml',$appxml); 
	
	}catch(Exception $e){
		 
	} 
	
	echo "<script type='text/javascript' src='images/check.js'></script> ";
	echo "<script>alert(decodeURI('%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F'));
			location.href='index.php'</script>";

?> 
 
