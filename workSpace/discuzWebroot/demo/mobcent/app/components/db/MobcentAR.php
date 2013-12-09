<?php

class MobcentAR extends CActiveRecord {

	protected function testDbConnection($db) {
		if ($db instanceof CDbConnection) {
			return true;
		} else {
			throw new CDbException(Yii::t('yii','Active Record requires a "db" CDbConnection application component.'));
		}
	}
}