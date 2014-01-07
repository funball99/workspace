<?php
require_once './abstarctcommonModule.php';
define ( 'IN_MOBCENT', 1 );
require_once '../model/class_core.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
$discuz = & discuz_core::instance();
$discuz->init();
require_once '../model/table/x20/topic.php';
require_once '../model/table/x20/mobcentDatabase.php';

class commonModuleImpl_x20 extends abstarctcommonModule {
	public function getcommonModuleObj() {
		$uselessbids = $usingbids = $bids = array();
		$query = DB::query("SELECT bid FROM ".DB::table('common_block')." WHERE blocktype='0' ORDER BY bid DESC LIMIT 1000");
			while($value = DB::fetch($query)) {
				$bids[] = intval($value['bid']);
			}
		$query = DB::query("SELECT bid FROM ".DB::table('common_template_block')." WHERE bid IN (".dimplode($bids).")");
		while(($value = DB::fetch($query))) {
			$usingbids[] = intval($value['bid']);
		}
		$uselessbids = array_intersect($bids, $usingbids);
		$uselessbids =implode(',',$uselessbids);
		global $_G;
		block_get($uselessbids);
		$classList = explode(',',$uselessbids);
		$xm=new topic();
		$s=file_exists('../manage/App.xml')?join("",file('../manage/App.xml')):array();
		$result =$xm->xml_to_array($s);
		switch($result['version'][0][0][0])
		{
			case 1:
				foreach($result['classFid']['classItem'] as $val)
				{
					$classItem[]=!empty($val) && !is_array($val)?$val:$val[0][0];
				}
				foreach($classList as $key=>$val){
					if(!empty($_G['block'][$val]['name'])){
						if(!empty($_G['block'][$val]['name']) && in_array($val,$classItem)){
							$moudleData ['moduleId'] =$val;
							$moudleData ['moduleName'] =$_G['block'][$val]['name'];
							$dataList[]=$moudleData;
						}
							
					}
				}
				$data['rs'] = 1;
				$data['list'] = !empty($dataList)?$dataList:array();
				return $data;
				break;
			case 2:
				$Img_list=DB::query(" SELECT * FROM ".DB::table('add_module')." ORDER BY display desc limit 0,6");
				while($Img_value = DB::fetch($Img_list)) {
					$ImgArr[] = $Img_value;
				}
				if($_GET['sdkVersion']!="1.0.0"){ //old version
					foreach($ImgArr as $key=>$val){
						if($key==0)
							continue;
						$moudleData ['moduleId'] =$val['id'];
						$moudleData ['moduleName'] =UC_DBCHARSET=='utf8'?$val['mname']:Common::get_unicode_charset($val['mname']);
						$moudleData ['moduleName'] = str_replace("\\\\","\\",$moudleData ['moduleName']);
						$dataList[]=$moudleData;
					}
				}else{
					foreach($ImgArr as $key=>$val){
						/*if($key==0) continue;*/
						$moudleData ['moduleId'] = (Int)$val['id'];
						$moudleData ['moduleName'] =UC_DBCHARSET=='utf8'?$val['mname']:Common::get_unicode_charset($val['mname']);
						$moudleData ['moduleName'] = str_replace("\\\\","\\",$moudleData ['moduleName']);
						$dataList[]=$moudleData;
					}
					if(!empty($dataList)){
						$dataList[0]['moduleId']=0;
						$dataList[0]['showflash']=1;
					}
					$menhu[]=array("moduleId"=>"0",
							"moduleName"=>UC_DBCHARSET=='utf8'?'\u95e8\u6237':Common::get_unicode_charset('\u95e8\u6237'),"showflash"=>1);
				}
				$data['rs'] = 1;
				$data['list'] = !empty($dataList)?$dataList:$menhu;
				return $data;
				break;	
		}
			
	}

}

?>
