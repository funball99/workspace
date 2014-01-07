<?php
require_once './abstarctcommonModule.php';
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../../config/config_ucenter.php';
require_once '../model/table_surround_user.php';
require_once '../tool/tool.php';
require_once '../model/table/x25/topic.php';
require_once '../model/table/x25/table_add_portal_module.php';
define('ALLOWGUEST', 1);
C::app ()->init ();

class commonModuleImpl_x25 extends abstarctcommonModule {
	public function getcommonModuleObj() {
		$xm=new topic();
		$s=file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
		$result =$xm->xml_to_array($s); 
		switch($result['version'][0][0])
		{
			case 1:
				$uselessbids = $usingbids = $bids = array();
				$bids = C::t('common_block')->fetch_all_bid_by_blocktype(0,1000);
				$usingbids = array_keys(C::t('common_template_block')->fetch_all_by_bid($bids));
				$uselessbids = array_intersect($bids, $usingbids);
				$uselessbids =implode(',',$uselessbids);
				global $_G;
				block_get($uselessbids);
				$classList = explode(',',$uselessbids);
				
				$s=file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
				$result =$xm->xml_to_array($s);
				foreach($result['classFid']['classItem'] as $val)
				{
					$classItem[]=!empty($val) && !is_array($val)?$val:$val[0];
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
				$ImgArr = add_portal_module::check_module(); 
				$dhList = add_portal_module::check_module_daohang();
				
				if($_GET['sdkVersion']!="1.0.0"){ //old version
					foreach($ImgArr as $key=>$val)
					{
						if($val['id'] ==$dhList[0]['id'])
							continue;
						$moudleData ['moduleId'] =$val['id'];
						$moudleData ['moduleName'] =UC_DBCHARSET=='utf8'?$val['mname']:Common::get_unicode_charset($val['mname']);
						$moudleData ['moduleName'] = str_replace("\\\\","\\",$moudleData ['moduleName']);
						$dataList[]=$moudleData;
					}
					$data['rs'] = 1;
					$data['list'] = !empty($dataList)?$dataList:array();
				}else{
					foreach($ImgArr as $key=>$val)
					{
						/*if($val['id'] ==$dhList[0]['id']) continue;*/
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
					$data['rs'] = 1;
					$data['list'] = !empty($dataList)?$dataList:$menhu;
				}
				return $data;
				break; 
		}
	}
}

?>