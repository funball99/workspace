<?php

/**
 * 分享（帖子、资讯）接口
 *
 * @author  任兴  
 */
class ShareAction extends CAction{
	public function run(){
        $res = WebUtils::initWebApiArray();
        $json = ($_GET['setting']);
        /*
        $json='
          {
              "head":{
                  "errCode":0,
                  "errInfo": "",
              },
              "body":{
                     "shareInfo":{ "shareId":1,"shareType":"news"},
                     "externInfo": {
                    },
              },
         }';*/
        
      $data = WebUtils::jsonDecode($json); 
      $version = $_GET['sdkVersion']?$_GET['sdkVersion']:'';
      $shareData = $data !=null ? $data['body']['shareInfo'] : array();
      
      if(empty($shareData)){
          $res = array_merge(array('rs' => 0, 'errcode' => '30000000'), $res);
      }else{
          $res = array_merge(array('rs' => 1, 'errcode' => 0), $res);
          switch($shareData['shareType']){
              case 'topic':
                  $shareContent=ForumUtils::getTopicInfo($shareData['shareId']);
                  $content = '';
                  $res['body']['shareData'] = array('title'=>$shareContent['subject'],
                                                    'content'=>$content,
                                                    'source'=>date("Y-m-d H:i:s",$shareContent['dateline']));
          
                  break;
              case 'news':
              	  $shareContent=PortalUtils::getNewsInfo($shareData['shareId']);
              	  $content = '';
              	  $res['body']['shareData'] = array('title'=>$shareContent['title'],
              										'content'=>$content,
              										'source'=>date("Y-m-d H:i:s",$shareContent['dateline']));
                  break;
          }
      }
      echo WebUtils::jsonEncode($res);
   }
}