<?php

class TestController extends Controller
{
    public function actionIndex()
    {
        echo 'mobcent test';
    }

    public function actionPhpInfo()
    {
        phpinfo();
    }

    public function actionVersion()
    {
        echo 'mobcent discuz version: ' . MobcentDiscuz::getMobcentDiscuzVersion() . '<br />';
        echo 'discuz version: ' . MobcentDiscuz::getDiscuzVersion() . '<br />';
        echo 'mobcent version: ' . MOBCENT_VERSION . '<br />';
        echo 'mobcent release: ' . MOBCENT_RELEASE . '<br />';
    }

    public function actionUrlRewrite()
    {
        echo 'url rewrite succeed!!!';
    }
}