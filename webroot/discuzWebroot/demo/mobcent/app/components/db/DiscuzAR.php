<?php

class DiscuzAR extends MobcentAR {

	public function getDbConnection() {
		return Yii::app()->dbDz;
	}
}