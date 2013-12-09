<?php                        

ob_end_clean ();
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../model/table_common_member_profile.php';
require_once '../../config/config_ucenter.php';
require_once '../Config/public.php';
require_once '../tool/tool.php';
require_once '../public/page.class.php';
define('ALLOWGUEST', 1);
C::app ()->init ();
require_once libfile ( 'function/forumlist' );
require_once '../model/table/x25/table_add_portal_module.php';

$id = $_REQUEST['mid'];
$arr = add_portal_module::check_module_edit($id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title></title>
	<link rel="stylesheet" href="images/showmanage.css" />
	<link rel="stylesheet" type="text/css" href="wbox/wbox.css" />
	<script type="text/javascript" src="wbox/jquery1.4.2.js"></script> 
	<script type="text/javascript" src="wbox/wbox.js"></script> 
	<script type="text/javascript" src="images/check.js"></script> 
</head>
<body>
<form action ='moduleListDelete.php' method='post'>
	<div id="showmanage">
		<div class="showmanage_content">
			<?php echo Common::get_web_unicode_charset($arr[0][mname])?>
		</div>
		 <div class="showmanage_content">
			<span class="showmanage_content_one"><?php if($arr[0][content]==2) echo Common::get_web_unicode_charset('\u81ea\u52a8\u6dfb\u52a0'); else echo Common::get_web_unicode_charset('\u624b\u52a8\u6dfb\u52a0 '); ?></span>
			<span class="showmanage_content_two"><?php if($arr[0][content]==2) echo Common::get_web_unicode_charset('\uff08\u6700\u591a\u53ef\u6dfb\u52a0\u0031\u6761\u6570\u636e\uff09 ')?></span>
		</div>
		<a href="" class="showmanage_moduleslink"  id='AddInfo'>+&nbsp;<?php echo Common::get_web_unicode_charset('\u70b9\u51fb\u6dfb\u52a0\u8d44\u8baf')?></a>
		<table>
			<tr class="title">
				<td style="width:30px;"></td>
				<td style="text-align:center;width:800px;"><?php echo Common::get_web_unicode_charset('\u540d\u79f0')?></td>
				<td style="width:130px;text-align:center;"><?php echo Common::get_web_unicode_charset('\u7c7b\u522b')?></td>
				<td style="width:130px;text-align:center;"><?php echo Common::get_web_unicode_charset('\u64cd\u4f5c')?></td>
			</tr>
			<?php 

			$pageCurrent=empty($_GET["page"])?1:$_GET["page"];
			$limit =20;
			$page_size=20;
			$start = ($pageCurrent -1)*$page_size;
			$sub_pages=5;
			$count = DB::fetch_first(" SELECT count(*) as num FROM %t where mid=%d AND isimage !=1 ",array('add_portal_module',$id));
			$nums=$count['num'];
			$data = DB::fetch_all(" SELECT * FROM %t where mid=%d AND isimage !=1  ORDER BY time DESC limit %d,%d",array('add_portal_module',$id,$start,$limit));
			if(!empty($data))
			{
				
				foreach($data as $key=>$val)
				{
					if($val['cidtype'] =='tid')
					{
						$thread = get_thread_by_tid($val[cid]);
						$info='\u005b\u5e16\u5b50\u005d'.unicode_encode($thread['subject']);
					}else if($val['cidtype'] =='aid'){
						$thread = C::t('portal_article_title')->fetch($val[cid]);
						$info='\u005b\u6587\u7ae0\u005d'.unicode_encode($thread['title']);
					}else if($val['cidtype'] =='bid'){
						$forum = C::t('forum_forum')->fetch_info_by_fid($val[cid]);
						$info='\u005b\u793e\u533a\u7248\u5757\u005d '.unicode_encode($forum['name']);
					}else{
						$forum = DB::fetch_all("SELECT catid, catname FROM ".DB::table('portal_category')." where catid = $val[cid]");
						$info='\u005b\u6587\u7ae0\u5206\u7c7b\u005d '.unicode_encode($forum[0]['catname']);
					}
				?>
			<tr>
			<input type="hidden"  value='<?php echo $arr[0]['content'];?>' name='cid' id='cid'/>
			<input type="hidden"  value='<?php echo $id;?>' name='mid' id='mid'/>
				<td><input class="showmanage_table_input" type="checkbox" value='<?php echo $val['id'];?>' name='lid[]'/></td>
				<td class="showmanage_table_content"><?php echo Common::get_web_unicode_charset($info);?></td>
				<td style="width:130px;text-align:center;">
				<?php $str=$val['cidtype'];
					switch($str){
						case 'aid':
							echo Common::get_web_unicode_charset('\u6587\u7ae0\u0049\u0044');
						break;
						case 'bid':
							echo Common::get_web_unicode_charset('\u793e\u533a\u7248\u5757');
						break;
						case 'fid':
							echo Common::get_web_unicode_charset('\u6587\u7ae0\u5206\u7c7b');
						break;
						case 'tid':
							echo Common::get_web_unicode_charset('\u5e16\u5b50\u0049\u0044');
							break;
					}
				?></td>
				<td><a class="delete" href="moduleListDelete.php?lid=<?php echo $val['id'];?>&mid=<?php echo $id;?>" onclick='return delconfirm()'><?php echo Common::get_web_unicode_charset('\u5220\u9664')?></a></td>
			</tr>
			<?php 
			}
			?>
		
			<tr>
				<td><input class="showmanage_table_input" type="checkbox" onclick="selectAllModuleList(this)" /></td>
				<td colspan="3">
					<span class="all" ><?php echo Common::get_web_unicode_charset('\u5168\u9009')?></span>
					<a  class="delete1"  onclick='return delconfirm()'><input type='submit' value='<?php echo Common::get_web_unicode_charset('\u5220\u9664')?>'></a>
					<span class="allpage"><?php echo Common::get_web_unicode_charset('\u6bcf\u9875\u663e\u793a\u0032\u0030\u6761')?></span>
					<div id="page">
						<?php $subPages=new SubPages($page_size,$nums,$pageCurrent,$sub_pages,"moduleList.php?mid=$id&page=",2);  ?>
						
					</div>
				</td>
			</tr>
				<?php 
			}
		?>
		<input type="hidden" id="hiddenRadioValue" value='<?php echo $id;?>'/>
		</table>
		<div class="button">
			<input class="return" type="button" onclick="javascript:location.href='index.php'" style="cursor:pointer"/>
		</div>
	</div>
	</form>
</body>
<script type="text/javascript">
function delconfirm(){
	  if(window.confirm(decodeURI('%E7%A1%AE%E5%AE%9A%E5%88%A0%E9%99%A4%EF%BC%9F'))){
	   return true;
	  }
	  return false;
	}
var str=$("#hiddenRadioValue").val();
$("#AddInfo").click(function(){
	var mid=$("#mid").val();
	var content=$("#cid").val();
	
	$.ajax({
		type:'POST',
		url: "addinfo.php?mid="+mid,
		success: function(result) {
             if(result >0 && content==2)
             {
           	   alert(decodeURI('%E5%86%85%E5%AE%B9%E6%9D%A5%E6%BA%90%E4%B8%BA%E2%80%9C%E8%87%AA%E5%8A%A8%E2%80%9D%E6%97%B6%EF%BC%8C%E5%8F%AA%E5%8F%AF%E6%B7%BB%E5%8A%A0%E4%B8%80%E6%9D%A1%E6%95%B0%E6%8D%AE%EF%BC%8C%E8%AF%B7%E5%88%A0%E9%99%A4%E5%90%8E%E5%86%8D%E6%B7%BB%E5%8A%A0%EF%BC%81'));window.location.href='index.php';
             }else{
            	
             }
       	 
         }
		})
})

	$("#AddInfo").wBox({requestType:"iframe",iframeWH:{width:470,height:350},target:"moduleListInfo.php?id="+str});

  
  </script>
</html>