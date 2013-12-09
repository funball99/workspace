<?php 
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../../source/function/function_forumlist.php';
require_once '../tool/tool.php';
require_once '../model/table/x20/topic.php';
require_once '../Config/public.php';
require_once '../public/page.class.php';

define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
define('IN_MOBCENT',1);
require_once '../install/checkModule.php';

$typeid=intval($_GET['typeid']);

$pageCurrent=empty($_GET["page"])?1:$_GET["page"];
$limit =20;
$page_size=20;
$start = ($pageCurrent -1)*$page_size;
$sub_pages=5;
$count = DB::fetch_first(" SELECT count(*) as num FROM ".DB::table('add_portal_module')." where mid=".$typeid." AND isimage=0");
$nums=$count['num'];

$news_list = DB::query("SELECT * FROM ".DB::table('add_portal_module')." where mid=".$typeid." AND isimage=0 ORDER BY time desc limit ".$start.",".$limit);
while($value = DB::fetch($news_list)) {
	$news[] = $value;
}
$newstype_list = DB::query("SELECT * FROM ".DB::table('add_module')." where id=".$typeid);
while($value = DB::fetch($newstype_list)) {
	$data[] = $value;
} 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title></title>
	<link rel="stylesheet" href="images/showmanage.css" />
	<link rel="stylesheet" type="text/css" href="wbox/wbox.css" />
	<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
	<script type="text/javascript" src="wbox/mapapi.js"></script> 
	<script type="text/javascript" src="wbox/wbox.js"></script> 
	<script type="text/javascript" src="images/check.js"></script>	 
</head> 
<body>  
<form action ='newsSubmit.php?act=delete' method='post'>
	<div id="showmanage">
		<div class="showmanage_content">
			<?php echo Common::get_web_unicode_charset($data[0][mname])?>	
		</div>
		<div class="showmanage_content">
			<span class="showmanage_content_one"><?php if($data[0][content]==2) echo Common::get_web_unicode_charset('\u81ea\u52a8\u6dfb\u52a0'); else echo Common::get_web_unicode_charset('\u624b\u52a8\u6dfb\u52a0 ');?></span>
			<span class="showmanage_content_two"><?php if($data[0][content]==2) echo Common::get_web_unicode_charset('\uff08\u6700\u591a\u53ef\u6dfb\u52a0\u0031\u6761\u6570\u636e\uff09 ')?></span>
		</div>
		<a class="showmanage_moduleslink" href="" id="addnews">+&nbsp;<?php echo Common::get_web_unicode_charset('\u70b9\u51fb\u6dfb\u52a0\u8d44\u8baf')?></a>
		<table>
			<tr class="title">
				<td style="width:30px;"></td>
				<td style="text-align:center;width:800px;"><?php echo Common::get_web_unicode_charset('\u540d\u79f0')?></td>
				<td style="width:130px;text-align:center;"><?php echo Common::get_web_unicode_charset('\u64cd\u4f5c')?></td>
			</tr>
			<?php 
		if($news!=""){
			foreach($news as $key=>$val){ 
				if($val['cidtype'] =='tid')
				{
					$thread_list = DB::query("SELECT subject FROM ".DB::table('forum_thread')." where tid=".$val[cid]);
					while($thread_value = DB::fetch($thread_list)) {
						$thread = $thread_value;
					} 
					$info='\u005b\u5e16\u5b50\u005d'.unicode_encode($thread['subject']);
				}else if($val['cidtype'] =='aid'){
					$portal_list = DB::query("SELECT title FROM ".DB::table('portal_article_title')." where aid=".$val[cid]);
					while($portal_value = DB::fetch($portal_list)) {
						$portal= $portal_value;
					} 
					$info='\u005b\u6587\u7ae0\u005d'.unicode_encode($portal['title']);
				}else if($val['cidtype'] =='bid'){
					$border_list = DB::query("SELECT name FROM ".DB::table('forum_forum')." where fid=".$val[cid]);
					while($border_value = DB::fetch($border_list)) {
						$border= $border_value;
					}
					$info='\u005b\u793e\u533a\u7248\u5757\u005d '.unicode_encode($border['name']);
				}else{
					$cat_list = DB::query("SELECT catname FROM ".DB::table('portal_category')." where catid=".$val[cid]);
					while($cat_value = DB::fetch($cat_list)) {
						$cat= $cat_value;
					}
					$info='\u005b\u6587\u7ae0\u5206\u7c7b\u005d '.unicode_encode($cat['catname']);
				} 
			 
?>
			<tr>
				<td><input class="showmanage_table_input" type="checkbox"  name="fid[]" value="<?php echo $val['id'];?>"/></td>
				<td class="showmanage_table_content"><?php echo Common::get_web_unicode_charset($info);?></td>
				<td><a class="delete" href="newsSubmit.php?act=del&id=<?php echo $val['id'];?>" onclick='return delconfirm()'>
				<?php echo Common::get_web_unicode_charset('\u5220\u9664')?></a></td>
			</tr>
			<?php } ?>
			 
			<tr> 
				<td><input class="showmanage_table_input" type="checkbox" onclick="selectAll(this);" /></td>
				<td colspan="2">
					<span class="all"><?php echo Common::get_web_unicode_charset('\u5168\u9009')?></span>
					<a class="delete1" onclick='return delconfirm()'>
					<input type='submit' value='<?php echo Common::get_web_unicode_charset('\u5220\u9664')?>'></a>
										
					<span class="allpage" style="padding-left:50px"><?php echo Common::get_web_unicode_charset('\u6bcf\u9875\u663e\u793a')?>20<?php echo Common::get_web_unicode_charset('\u6761')?></span>
					<div id="page">
						<?php $subPages=new SubPages($page_size,$nums,$pageCurrent,$sub_pages,"news.php?typeid=$typeid&page=",2);  ?>
					</div>
				</td>
			</tr>
			<?php }?>
		</table>
		<div class="button">			
			<input class="return" type="button" onclick="javascript:location.href='index.php'"  style="cursor:pointer"/>
		</div>
	</div>
	<div id="typeid" style="visibility:hidden"><?php echo $typeid; ?></div>
	<input type="hidden"  value='<?php echo $data[0]['content'];?>' name='cid' id='cid'/>
			<input type="hidden"  value='<?php echo $typeid;?>' name='mid' id='mid'/>
</form>

<script type="text/javascript">
   var typeid=$("#typeid").text();
   $("#addnews").click(function(){
		var mid=$("#mid").val();
		var content=$("#cid").val();
		
		$.ajax({
			type:'POST',
			url: "addinfo.php?mid="+mid,
			success: function(result) {
	             if(result >0 && content==2)
	             {
	            	 $('#AddInfo').wBox({target:"#AddInfo"});
	           	   alert(decodeURI('%E5%86%85%E5%AE%B9%E6%9D%A5%E6%BA%90%E4%B8%BA%E2%80%9C%E8%87%AA%E5%8A%A8%E2%80%9D%E6%97%B6%EF%BC%8C%E5%8F%AA%E5%8F%AF%E6%B7%BB%E5%8A%A0%E4%B8%80%E6%9D%A1%E6%95%B0%E6%8D%AE%EF%BC%8C%E8%AF%B7%E5%88%A0%E9%99%A4%E5%90%8E%E5%86%8D%E6%B7%BB%E5%8A%A0%EF%BC%81'));window.location.href='index.php';
	             }else{
	            	
	             }
	       	 
	         }
			})
	})
   $("#addnews").wBox({requestType:"iframe",iframeWH:{width:400,height:180},target:"newsAdd.php?typeid="+typeid});
 
   function delconfirm(){		   
	  if(window.confirm(decodeURI('%E7%A1%AE%E5%AE%9A%E5%88%A0%E9%99%A4%EF%BC%9F'))){
	  	return true;
	  }
	 	return false;
	}	
   function selectAll(checkbox) { 
		$('input.showmanage_table_input[type=checkbox]').attr('checked', $(checkbox).attr('checked')); 
	} 
</script>
	
</body>
</html>