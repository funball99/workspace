<?php

class SquareController extends MobcentController 
{
    protected function beforeAction($action)
    {
        parent::beforeAction($action);

        $canCheck = true;

        if ($action->id == 'surrounding' && $_G['poi'] == 'topic')
            $canCheck = false;

        if ($canCheck)
            $this->checkUserAccess();

        return true;
    }

    public function actions()
    {
        return array(
            'surrounding' => 'application.controllers.square.SurroundingAction',
        	'share' => 'application.controllers.square.ShareAction',
        );
    }
}