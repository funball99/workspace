<?php

class SettingAction extends CAction
{
    public function run($setting)
    {
        $res = WebUtils::initWebApiArray();
        $res = array_merge(array('rs' => 1, 'errcode' => 0), $res);

        $uid = $this->getController()->uid;

        // test 
        // $_GET['setting'] ='{"head": {"errCode": 0, "errInfo": ""}, "body": {"settingInfo": {"hidden": 1}, "externInfo": {}}}';
        $settings = rawurldecode($setting);
        $settings = WebUtils::jsonDecode($settings);
        $settings = $settings != null ? $settings['body']['settingInfo'] : array();

        // insert or update new settings
        UserSetting::saveNewSettings($uid, $settings);

        echo WebUtils::jsonEncode($res);
        Yii::app()->end();
    }
}