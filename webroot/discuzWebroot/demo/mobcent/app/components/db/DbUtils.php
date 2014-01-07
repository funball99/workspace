<?php

class DbUtils {

	public static function getDiscuzCommand() {
		return Yii::app()->dbDz->createCommand();
	}

	public static function getDzUCenterCommand() {
		return Yii::app()->dbDzUc->createCommand();
	}
}