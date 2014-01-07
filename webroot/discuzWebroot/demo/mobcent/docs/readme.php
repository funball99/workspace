<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="content-language" content="en"/>
<title>安米网插件安装说明文档</title>
<link rel="stylesheet" href="../index/images/anmi2discuse.css" />
</head>

<body style="margin:0;padding:0;">
<div id="discuse">
		<div style="margin-left:10px"><h2>安米网插件安装说明文档</h2></div>
		<div class="discuse_content" style="font-size:14px">
		<p>
		<strong>QQ咨询：</strong><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=465017862&site=qq&menu=yes">
		<img border="0" src="http://wpa.qq.com/pa?p=2:465017862:41" alt="QQ Service" title="QQ Service"/></a></p>
		
		<p><strong>安米官方QQ群：</strong>
		安米-DZ站长交流1群：<font color='red'>173534670</font>   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
		安米-DZ站长交流2群：<font color='red'>100162461</font> </p>
		
			<div style="height:21px;">
			<h3>安装步骤：</h3> 
			<p>第一步：解压Mobcent压缩包，并把解压后文件用FTP上传到您的Discuz!系统根目录下面，进入discuz目录，将mobcent文件夹权限赋予777 例如:chmod -R 777 mobcent<br />
			   第二步：运行“您的论坛域名+/mobcent/index.php”例如:http://www.mobcent.com/mobcent/index.php <br />
			  第三步：根据引导正确安装插件<br />
			 第四步：填写打包密码<br />
			 第五步：插件安装成功！<br />
		             第六步：返回安米网（www.appbyme.com）继续转换应用。</p>
			  
			<h3>注意事项：</h3>
			<p>
			 1.安装插件时设置的转换密码存放在数据库中，如果忘记密码可以进入管理页面修改密码，或删除网站根目录下mobcent.log文件重新安装。<br />
			 2.论坛转换打包完成后系统会自动把Android应用包和ios应用包放到Discuz!所属服务器上，Android应用包在mobcent\data\android目录里，ios应用包在mobcent\data\ios目录里（请勿删除）<br />
			 3.内容管理页面地址为：域名+/mobcent/index.php[Discuz X2.5和X3.0版本]或者： 域名+/mobcent/manage_x20/login.php[discuz X2.0版本]（打开页面需要输入安装插件时设置的账号和密码）<br/>
			  4.门户幻灯片最多为5张，门户导航最多为6个<br />
			  5.更新插件时为避免出错请先删除上一次安装的“mobcent”文件包再进行安装新版！</p>
			  
			<h3>热门问题：</h3>
			<p>Q1：对于windows服务器，下载安卓应用和ios应用提示404错误?<br />
			 A: 首先到mobcent/data/android 或者mobcent/data/ios下看是否存在安卓应用或ios应用，如果存在请打开IIS管理器，再打开“本地计算机”的属性——>MIME类型——>新建 即可。<br />
			  例如：扩展名：.apk MIME类型：applicationnd/vnd.android.package 重启IIS即可 </p>
			  
			<p> Q2：刚安装的应用，打开都提示数据解析异常？<br />
			 A：请确认是否执行了您的论坛域名+/mobcent/index/install/index.php</p>
			 
			 <p>Q3：打包验证一直失败为什么？<br />
			 A3：1.先检查密码是否输入正确。2.mobcent是否放到了根目录。3.域名是否是80端口 例如：***:9192这样的端口是无法打包的。</p>
			 
			<p id="pdo">Q4: windows下如何安装pdo扩展?<br />
			 A4:编辑 php.ini 文件：将 extension=php_pdo.dll 前面的分号“;”去掉即可, <br />
			如还需要打开相应的数据库驱动扩展模块，例如mysql, 同样去掉extension=php_pdo_mysql.dll前面的";"即可,<br />
			编辑好之后重新启动 Web 服务器(apache/IIS/nginx)。 <br />
			详细参考资料：<a href="http://baike.baidu.com/link?url=SiTaI4kFatfoDGCL_xsvzhUmjcc5PwcvRQy3FgimzqSs2R4oZ_98PyptbqzcePcvd_Yi_AEIvQZ0dbRMF0BRaK#6" target="_blank">点击查看</a>
			</p>
			<p id="curl">Q5: windows下如何安装cURL扩展?<br />
			A5: 1. 修改php.ini, 去掉 extension=php_curl.dll 前面的分号.
			    2. 拷贝PHP目录中的libeay32.dll 和 ssleay32.dll 两个文件到 Windows系统目录(一般为 c:\windows\system32).
			</div>
		</div>
</body>

</html>
 
