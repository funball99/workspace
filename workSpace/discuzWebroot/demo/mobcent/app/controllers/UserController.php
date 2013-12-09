<?php

class UserController extends MobcentController 
{
    protected function beforeAction($action)
    {
        parent::beforeAction($action);

        if ($action->id != 'qqlogin')
            $this->checkUserAccess();

        return true;
    }

    public function actions()
    {
        return array(
            'setting' => 'application.controllers.user.SettingAction',
            'qqlogin' => 'application.controllers.user.QQLoginAction',
        );
    }
}