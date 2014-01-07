<?php

class QQLoginAction extends CAction
{
    public function run()
    {
        if (MobcentDiscuz::getDiscuzVersion() == 'X3.1') 
            $this->_run_x31();
        else
            $this->_run();
    }

    private function _run()
    {
        $path = Yii::getPathOfAlias('application.components.discuz.qqconnect');
        require_once($path . '/connect_login_x25.php');
    }

    private function _run_x31()
    {
        $this->_run();
    }
}