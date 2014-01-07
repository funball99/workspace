<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
@set_magic_quotes_runtime(0);

define('IN_COMSENZ', TRUE);
define('ROOT_PATH', dirname(__FILE__).'/../');
define('CONFIG', '../config/config_global.php');
require 'include/install_mysql.php';
require 'include/install_var.php';
include ROOT_PATH.CONFIG;
define('UC_DBCHARSET',$_config['db'][1]['dbcharset']);
@header("Content-type: text/html; charset=utf8");
require_once '../Config/public.php';
$mobcentPath = dirname(__FILE__) . "/../";
$ucserverPath = dirname(__FILE__) . "/../../uc_server";

$dirfile_items = array
(
		'mobcent_data' => array('type' => 'dir', 'path' => $mobcentPath,'name' => 'mobcent'),
		'uc_server' => array('type' => 'dir', 'path' => $ucserverPath,'name' => 'uc_server'),
);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo UC_DBCHARSET;?>" />
	<title></title>
	<link rel="stylesheet" href="images/css.css" />
</head>
<body style="margin:0px;padding:0px;">
	<div id="check" style="margin-left:auto;margin-right:auto;">
	 
		<div class="check_top">
			<div class="discur_xzaz">
			   <span><?php echo Common::get_web_unicode_charset('\u6469\u8baf\u0044\u0069\u0073\u0063\u0075\u007a\u0021\u4e0b\u8f7d\u5b89\u88c5');?></span>
			   <a href=""><img src="images/close.png" alt="" /></a>
		    </div>
			<div class="check_top_jc">
				<div><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u6570\u636e\u5e93\u662f\u5426\u8fde\u63a5\u6210\u529f');?></div>
				<div><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u6587\u4ef6\u5939\u662f\u5426\u6709\u6743\u9650');?></div>
				<div><?php echo Common::get_web_unicode_charset('\u5b89\u88c5');?></div>
			</div>
			<div class="check_top_img">
				<div class="jiantou"><img src="images/blue_jiantou.png" alt="" /></div>
				<div class="jiantou"><img src="images/green_jiantou.png" alt="" /></div>
				<div class="wancheng"><img src="images/grey_jiantou.png" alt="" /></div>
			</div>
		</div>
		<?php
			  if(file_exists('install.log') && file_get_contents('install.log')==file_get_contents('predefined.log'))
			  {
				echo "<script>window.location = 'index.php'</script>";
			  }else{?>
		<div class="check_middle">
			<span class="checkspan"><?php echo Common::get_web_unicode_charset('\u6570\u636e\u5e93\u8fde\u63a5\u68c0\u67e5');?></span>
			<table class="checktable">
				<tr class="tablehead">
					<td style="width:60%;"><?php echo Common::get_web_unicode_charset('\u51fd\u6570\u540d\u79f0');?></td>
					<td style="width:30%;"><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u7ed3\u679c');?></td>
					<td style="width:10%;"><?php echo Common::get_web_unicode_charset('\u5efa\u8bae');?></td>
				</tr>
				<tr class="tableother">
					<td>./config/config_global.php</td>
					<?php
						if(file_exists(ROOT_PATH.CONFIG)) {
							include ROOT_PATH.CONFIG;
						} else {
							$_config = $default_config;
						}
						
						$dbhost = $_config['db'][1]['dbhost'];
						$dbname = $_config['db'][1]['dbname'];
						$dbpw = $_config['db'][1]['dbpw'];
						$dbuser = $_config['db'][1]['dbuser'];
						define('DBCHARSET',$_config['db'][1]['dbcharset']);
						$tablepre = $_config['db'][1]['tablepre'];
						$dbname_not_exists = true;
						$db = new dbstuff;
						
						$db->connect($dbhost, $dbuser, $dbpw, $dbname, DBCHARSET);
						$open =opendir(ROOT_PATH.'./install/data/');
						$result = mysql_query('SHOW TABLES');
						while($rows = mysql_fetch_array($result))
						{
							$arr[]=$rows[0];
						}
						while($file = readdir($open))
						{
							if ($file == '.' || $file == '..' || $file =='.svn') continue;
							$sql = file_get_contents(ROOT_PATH.'./install/data/'.$file);
							$sql = str_replace('{replaceStr}', $tablepre, $sql);
							$sql = str_replace("\r\n", "\n", $sql);
							
							$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
							$type = in_array($type, array('MYISAM', 'HEAP', 'MEMORY')) ? $type : 'MYISAM';
							$sql = preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
							(mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=".UC_DBCHARSET : " TYPE=$type");
							$prename=str_replace('.sql', '', $file);
							if(in_array($tablepre.$prename, $arr) && $tablepre.$prename ==$tablepre.'add_portal_module') 
							{
								$columnsQuery =mysql_query('SELECT * FROM '.$tablepre.'add_portal_module');
								$columnsArr = mysql_field_name($columnsQuery,10);
								if($columnsArr != 'essence')
								{
									mysql_query('DROP  TABLE '.$tablepre.'add_portal_module');
									$create_table = mysql_query($sql) or die(mysql_errno());
									if($create_table == 1)
									{
										$issubmit = true;
										$istable = true;
									}
								}
							
							}
							if(in_array($tablepre.$prename, $arr) &&  $tablepre.$prename ==$tablepre.'add_module')
							{
								
								$columnsQuery =mysql_query('SELECT * FROM '.$tablepre.'add_module');
								$columnsArr = mysql_field_name($columnsQuery,3);
								if($columnsArr != 'content'){
									mysql_query('DROP  TABLE '.$tablepre.'add_module');
									$create_table = mysql_query($sql) or die(mysql_errno());
									if($create_table == 1)
									{
										$issubmit = true;
										$istable = true;
									}
								}
								
							}
							if(in_array($tablepre.$prename, $arr))
							{
								$issubmit = true;
								$istable = true;
							
							}
							else {
								$create_table = mysql_query($sql) or die(mysql_errno());
								if($create_table == 1)
								{
									$issubmit = true;
									$istable = true;
								}
								else {
									$issubmit = false;
									$istable = false;
								}
							}
						}
						

					?>
			<td class="lj"><?php if($istable == true){ echo Common::get_web_unicode_charset('\u8fde\u63a5\u6210\u529f');}else{ echo '<font color=red>'.Common::get_web_unicode_charset('\u8fde\u63a5\u5931\u8d25').'</font>';}?></td>
					<td><?php echo Common::get_web_unicode_charset('\u5199\u5165');?></td>
				</tr>
			</table>
		</div>
		<div class="check_middle">
		
			<span class="checkspan"><?php echo Common::get_web_unicode_charset('\u6587\u4ef6\u5939\u6743\u9650\u68c0\u67e5');?></span>
			<table class="checktable">
				<tr class="tablehead">
					<td style="width:60%;"><?php echo Common::get_web_unicode_charset('\u76ee\u6807\u6587\u4ef6');?></td>
					<td style="width:30%;"><?php echo Common::get_web_unicode_charset('\u68c0\u67e5\u7ed3\u679c');?></td>
					<td style="width:10%;"><?php echo Common::get_web_unicode_charset('\u5efa\u8bae');?></td>
				</tr>
				<?php 
					$dirfile_items = dirfile_check($dirfile_items);
					foreach ($dirfile_items as $key=>$val)
					{
						echo '<tr class="tableother">';
						echo "<td>".$val['name']."</td>";
						
						if(checkFile($val['current']))
						{
							echo '<td class="lj">'.Common::get_web_unicode_charset('\u53ef\u5199').'</td>';
						}else{
							$issubmit = false;
							echo "<td class='lj' style='color:red'>".Common::get_web_unicode_charset('\u53ea\u8bfb')."</td>";
						}
						echo '<td >'.Common::get_web_unicode_charset('\u53ef\u5199').'</td>';
						echo "</tr>";
					}
				?>

			</table>
		</div>
		
	<form action="stemp_2.php" class="check_bottom"  name="check_bottom" onsubmit ="return checkpwd()" method ="post">
		<div class="password">
			<span class="setspan"><?php echo Common::get_web_unicode_charset('\u8bbe\u7f6e\u8f6c\u6362\u5bc6\u7801\uff1a');?></span>
			<div class="import">
				<input type="password" name ='pwd' id ='pwd'/>
				<span class="passwords"><?php echo Common::get_web_unicode_charset('\uff08\u8bf7\u8f93\u5165\u0036\u4f4d\u6570\u5b57\u6216\u5b57\u6bcd\u4f5c\u4e3a\u8f6c\u6362\u5bc6\u7801\uff09');?></span>
			</div>
		</div>
		<input type="reset" value="<?php echo Common::get_web_unicode_charset('\u53d6\u6d88');?>" class="qx" />
			<?php
		if($issubmit =='true')
		{
			echo ' <input type="submit" value='.Common::get_web_unicode_charset('\u4e0b\u4e00\u6b65').' class="xyb" />';
		}
		else
		{
			echo ' <input type="submit" value='.Common::get_web_unicode_charset('\u4e0b\u4e00\u6b65').' class="xyb" disabled/>';
		}
		?>

		</form>
		<?php }?>
	</div>
	<script type="text/javascript">
		function checkpwd(){
			var pwd =document.getElementById('pwd').value;
			if(pwd.length < 6){
				alert('password length <6');
				return false;
				}
			
			}
	</script>
</body>
</html>