<?php   
while($list = DB::fetch($query)) {
	$result[] = $list;
}
	$newq = unicode_encode($newq);
	$newq = str_replace("\\","\\\\",$newq);
	$newa = unicode_encode($newa);
	$newa = str_replace("\\","\\\\",$newa); 
		echo "<script>alert(decodeURI('%E6%82%A8%E8%BE%93%E5%85%A5%E7%9A%84%E7%99%BB%E5%BD%95%E5%AF%86%E7%A0%81%E4%B8%8D%E6%AD%A3%E7%A1%AE%EF%BC%81'));history.back();</script>";
		exit();
	}
	echo "<script>alert(decodeURI('%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F%EF%BC%81'));location.href='../index.php';</script>";