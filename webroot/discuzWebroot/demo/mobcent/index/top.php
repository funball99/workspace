<?php 
require_once '../tool/tool.php';
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title></title>
	<link rel="stylesheet" href="images/anmi2discuse.css" />
	<link rel="stylesheet" href="images/anmi2discuse.css" />
	<link rel="stylesheet" type="text/css" href="wbox/wbox.css" />
	<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
	<script type="text/javascript" src="wbox/mapapi.js"></script> 
	<script type="text/javascript" src="wbox/wbox.js"></script> 
</head>
<body>
<div style="z-index:1000;" id="head">
		
		<div style="padding-left:0px;width:1000px;" id="head_nav">
			<div id="nav">
				<a href="index.php"><?php echo Common::get_web_unicode_charset('\u9996\u9875 ')?></a>
				<a href="manage.php"><?php echo Common::get_web_unicode_charset('\u5185\u5bb9\u7ba1\u7406')?></a>
				<a href="http://www.appbyme.com/mobcentACA/jsp/app/discuzInfo.jsp"  target="_blank"><?php echo Common::get_web_unicode_charset('\u8bf4\u660e\u6587\u6863')?></a>
				<a href="../download/down.php" target="blank"><?php echo Common::get_web_unicode_charset('\u5e94\u7528\u4e0b\u8f7d')?></a>
				<a href="../../" target="_blank"><?php echo Common::get_web_unicode_charset('\u7f51\u7ad9\u9996\u9875')?></a>
				<a href="login/logout.php" onclick="return confirm(decodeURI('%E7%A1%AE%E5%AE%9A%E9%80%80%E5%87%BA%EF%BC%9F'))" ><?php echo Common::get_web_unicode_charset('\u5b89\u5168\u9000\u51fa')?></a>
			</div> 
		</div>
</div>
</body> 
</html>
 