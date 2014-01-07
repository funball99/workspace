<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
@set_magic_quotes_runtime(0);

define('IN_DISCUZ', TRUE);
define('IN_COMSENZ', TRUE);
define('ROOT_PATH', dirname(__FILE__).'/../');
define('CONFIG', '../config/config_global.php');
include ROOT_PATH.CONFIG;
define('UC_DBCHARSET',$_config['db'][1]['dbcharset']);
@header("Content-type: text/html; charset=utf8");
define('ROOT_PATH', dirname(__FILE__).'/../../');
require_once '../Config/public.php';
$xml = simplexml_load_file('App.xml');

$AppIcon = $xml->AppIcon;
$AppImg = $xml->AppImg;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0062)http://appbyme.org/mobcentACA/app.do?appKey=n63V72crtotJt38NJV -->
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="expires" content="0">
		<link rel="alternate" type="application/vnd.wap.xhtml+xml" media="handheld" href="http://appbyme.org/mobcentACA/target">
		<title><?php echo Common::get_web_unicode_charset('\u5e94\u7528\u4e2d\u5fc3\u9875')?></title>
		<link href="./images/style.css" rel="stylesheet" type="text/css">
		<script src="./images/jquery-1.7.min.js">
		</script>
		
	<script charset="UTF-8" src="./images/bundle.js"></script><link type="text/css" rel="stylesheet" href="chrome-extension://cpngackimfmofbokmjmljamhdncknpmg/style.css"><script type="text/javascript" charset="utf-8" src="chrome-extension://cpngackimfmofbokmjmljamhdncknpmg/page_context.js"></script><script charset="UTF-8" src="./images/iframeWidget.js"></script><style>#haloword-pron { background: url(chrome-extension://bhkcehpnnlgncpnefpanachijmhikocj/img/icon.svg) -94px -34px; }#haloword-pron:hover { background: url(chrome-extension://bhkcehpnnlgncpnefpanachijmhikocj/img/icon.svg) -111px -34px; }#haloword-open { background: url(chrome-extension://bhkcehpnnlgncpnefpanachijmhikocj/img/icon.svg) -94px -17px; }#haloword-open:hover { background: url(chrome-extension://bhkcehpnnlgncpnefpanachijmhikocj/img/icon.svg) -111px -17px; }#haloword-close { background: url(chrome-extension://bhkcehpnnlgncpnefpanachijmhikocj/img/icon.svg) -94px 0; }#haloword-close:hover { background: url(chrome-extension://bhkcehpnnlgncpnefpanachijmhikocj/img/icon.svg) -111px 0; }#haloword-add { background: url(chrome-extension://bhkcehpnnlgncpnefpanachijmhikocj/img/icon.svg) -94px -51px; }#haloword-add:hover { background: url(chrome-extension://bhkcehpnnlgncpnefpanachijmhikocj/img/icon.svg) -111px -51px; }#haloword-remove { background: url(chrome-extension://bhkcehpnnlgncpnefpanachijmhikocj/img/icon.svg) -94px -68px; }#haloword-remove:hover { background: url(chrome-extension://bhkcehpnnlgncpnefpanachijmhikocj/img/icon.svg) -111px -68px; }</style><link rel="Stylesheet" type="text/css" charset="utf-8" href="./images/followWithCard.css"><div style="position: absolute; top: -9999px; left: -9999px;"></div></head>

	<body screen_capture_injected="true" youdao="bind"><iframe style="display: none;" id="sina_anywhere_iframe"></iframe>
		<!------------------head------------------>
		


<link rel="stylesheet" type="text/css" href="./images/app_cssnew.css">


<script type="text/javascript" src="./images/png.js"></script>
<script src="./images/wb.js" type="text/javascript" charset="utf-8"></script>
<!------------------head------------------>
<!--<div style="display:none" id="new_year_div"><img src="/mobcentACA/img/new-year.jpg" width="100%"/></div>
-->
<div id="head" style="z-index:1000;">
	<div id="head_nav"  style="padding-left:0px;width:900px;">
    	<div id="nav">
       	    <a href="/" ><?php echo Common::get_web_unicode_charset('\u8fd4\u56de')?></a>
            <a href="" id="index" class="selet"><?php echo Common::get_web_unicode_charset('\u4e0b\u8f7d\u9875\u9762')?></a>
        </div>
    </div>
</div>
<!--------------------head_end------------------>


		<script type="text/javascript">
$("#appmanage").addClass("selet");
</script>
		<!--------------------head_end------------------>

		<!-------------------main------------------->
		<div id="main">

			<!--updwonpc_box-->
			<div class="updownpc">
				<div class="updown_left">

					<table width="100%" border="0">
						<tbody><tr>
							<td width="80" rowspan="2" align="center">
								<img src="<?php echo $AppIcon; ?>" width="75" height="75">
							</td>
							<td>
								&nbsp;
							</td>
							<td>
								<font class="updownblue"><?php echo Common::get_web_unicode_charset($xml->AppName) ;?></font>
								<?php echo Common::get_web_unicode_charset('\u7248\u672c\u53f7\uff1a') ;echo Common::get_web_unicode_charset($xml->AppVersion); ?>
							</td>
							<td>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td width="10">
								&nbsp;
							</td>
							<td>
							<?php echo Common::get_web_unicode_charset('\u652f\u6301\u5e73\u53f0\uff1a\u0041\u006e\u0064\u0072\u006f\u0069\u0064\u0020\u002f\u0069\u0050\u0068\u006f\u006e\u0065') ;?>
								
							</td>
							<td align="right">
							<?php echo Common::get_web_unicode_charset('\u5f00\u53d1\u8005\uff1a');echo Common::get_web_unicode_charset($xml->AppAndroid); ?>
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;
							</td>
							<td colspan="3">
								&nbsp;&nbsp;&nbsp;<?php echo Common::get_web_unicode_charset('\u5e94\u7528\u63cf\u8ff0\u003a');echo Common::get_web_unicode_charset($xml->AppDescribe); ?>
						</tr>
					</tbody></table>
					
					
						<div class="updown_an">
							<table width="100%" border="0">
								<tbody><tr>
									<td>
										<a href="http://img.mobcent.com/mobcentFile/servlet/DownLoadFileServlet?action=apk&contentId=<?php echo $xml->AppID; ?>&appPlat=0" class="android_an"><img src="./images/android_an.gif" width="200" height="53">
										</a>
									</td>
									<td>
										<a href="http://img.mobcent.com/mobcentFile/servlet/DownLoadFileServlet?action=ipa&contentId=<?php echo $xml->AppID; ?>&appPlat=0" class="android_an"><img src="./images/iphone_an.gif" width="200" height="53">
										</a>
									</td>
								</tr>
								<tr>
									<td>
										&nbsp;
									</td>
									<td>
										&nbsp;
									</td>
								</tr>
								<tr>
									<td style="padding-left: 41px;">
										&nbsp;
									</td>
									<td style="padding-left: 41px;">
										&nbsp;
									</td>
								</tr>
								<tr>

									<td style="padding-left: 41px;">
										<img src="<?php echo $xml->androidPath; ?>" width="118" height="117">
									</td>
									<td style="padding-left: 41px;">
										<img src="<?php echo $xml->IosPath; ?>" width="118" height="117">
									</td>
								</tr>
								<tr><td></td><td><span style="font-size: 12px;"><?php echo Common::get_web_unicode_charset('\u6e29\u99a8\u63d0\u793a\u003a\u5df2\u8d8a\u72f1\u0069\u0050\u0068\u006f\u006e\u0065\u4e0b\u8f7d\u8f6f\u4ef6\u540e\u53ef\u76f4\u63a5\u901a\u8fc7\u0039\u0031\u52a9\u624b\u7b49\u5de5\u5177\u8fdb\u884c\u5b89\u88c5\u0021'); ?></span></td></tr>
								<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								</tr>
								<tr>
								<td colspan="2">
								</td>
								</tr>
							</tbody></table>
					
				</div>
			</div>
			<div class="updown_right">
				<img src="<?php echo $AppImg; ?>" width="295" height="445">
			</div>
			<div class="qing">
			</div>
			 

		</div>
		
		<!--updwonpc_box_end-->
		<!------------------main_end------------------>

		<!-------------footer------------------>
		   
    
    <style type="text/css">
	.yqlj{
		text-decoration:none;
		color:#000000!important;
		font-size:16px;
		display:block;
		width:120px;
		float:left;
		margin-left:22px;
		margin-top:10px;
	}
	.yqlj1{
		text-decoration:none;
		color:#000000;
		font-size:12px;
		width:48px;
	}
	.yqlj:hover{
		text-decoration:underline;
	}
	#footer_box a{
		color:#000!important;
	}
</style>
<div id="footer" style="background-color:#f6f6f6;">	
			<div style="clear:both;"></div>
	<div id="footer_box" style="z-index:1000;margin-left:auto;margin-right:auto;width:1000px;margin-top:50px;">
     
       
        <div id="help" style="width:85px;height:35px;line-height: 35px;bottom:0px;">
       
        </div>
        <div class="cb">
        </div>
    </div>
</div>

		<!------------------footer_end------------------>

</div>
		</body></html>