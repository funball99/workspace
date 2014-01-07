<?php

class MessageController extends MobcentController {

    const HEART_PERIOD = "40000";

	protected function beforeAction($action)
    {
        parent::beforeAction($action);

        $this->checkUserAccess();

		return true;
	}

	public function actions() {
		return array(
			'heart' => 'application.controllers.message.HeartAction',
			'pmlist' => 'application.controllers.message.PMListAction',
			'notifylist' => 'application.controllers.message.NotifyListAction',
		);
	}
}