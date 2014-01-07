<?php

class IndexController extends Controller {

	public function actionIndex() {
		echo 'welcome mobcent';
	}

	public function actionError() {
		if ($error = Yii::app()->errorHandler->error) {
			echo $error['message'];
		}
	}
}