<?php

class DiscuzUCenterAR extends MobcentAR {

	public function getDbConnection() {
		return Yii::app()->dbDzUc;
	}
}