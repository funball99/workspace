<?php

class MobcentController extends Controller
{
    public $uid = 0;
    public $rootUrl = '';
    public $dzRootUrl = '';

    public function init()
    {
        parent::init();

        $this->rootUrl = Yii::app()->getBaseUrl(true);
        $this->dzRootUrl = substr($this->rootUrl, 0, -16);
    }

    protected function beforeAction($action)
    {

        parent::beforeAction($action);

        // for test
        // $_GET['accessToken'] = '8d5478c77477933169ab8cfde10b5';
        // $_GET['accessSecret'] = 'a57002aab240f3ff831d868b623ff';
        // $_GET['sdkVersion'] = '1.0.0';
        
        return true;
    }

    protected function checkUserAccess()
    {
        $res = WebUtils::initWebApiArray();
        $access = UserUtils::checkAccess($_GET['accessToken'], $_GET['accessSecret']);
        if (!$access) {
            $res = array_merge(array('rs' => 0, 'errcode' => 50000000), $res);
            echo CJSON::encode($res);
            exit;
        }

        $this->uid = HomeAccess::getUserIdByAccess($_GET['accessToken'], $_GET['accessSecret']);
    }
}