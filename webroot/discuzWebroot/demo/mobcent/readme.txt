第一步：解压Mobcent压缩包，并把解压后文件用FTP上传到您的Discuz！系统根目录下面，进入discuz目录，将mobcent文件夹权限赋予777   例如:chmod -R 777 mobcent
第二步：运行“您的论坛域名+/mobcent/index.php”例如:http://www.mobcent.com/mobcent/index.php 
第三步：根据引导正确安装插件
第四步：填写打包密码
第五步：插件安装成功！
第六步：继续返回mobcent进行应用打包。

注意事项
1.安装插件时设置的转换密码存放在mobcent\install\AppPackPwd.xml文件中（请勿删除install文件夹，以便忘记密码时查看）
2.论坛转换打包完成后系统会自动把Android应用包和ios应用包放到Discuz！所属服务器上，Android应用包在mobcent\data\android目录里，ios应用包在mobcent\data\ios目录里（请勿删除）
3.新增加论坛管理页面，地址：域名+/mobcent/manage_x25/login.php（打开页面需要输入安装插件时设置的转换密码） 打包完后
4.门户幻灯片最多为五张
5.门户导航最多为6个
6.更新请先删除mobcent，以免出错

热门问题
Q1：对于windows服务器，下载安卓应用和ios应用提示404错误?
A: 首先到mobcent/data/android 或者mobcent/data/ios下看是否存在安卓应用或ios应用，如果存在请打开IIS管理器，再打开“本地计算机”的属性——》MIME类型——》新建 即可。
例如：扩展名：.apk   MIME类型：applicationnd/vnd.android.package 重启IIS即可
Q2：刚安装的应用，打开都提示数据解析异常？
A：请确认是否执行了您的论坛域名+/mobcent/index/install/index.php。
Q3：打包验证一直失败为什么？
A3：1.先检查密码是否输入正确。2.mobcent是否放到了根目录。3.域名是否是80端口 例如：***:9192这样的端口是无法打包的。
