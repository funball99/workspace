<?php

class HomeAccess extends DiscuzAR {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{home_access}}';
	}

	public function rules() {
		return array(
		);
	}

	// public function attributeLabels() {
	// 	return array(
	// 	);
	// }

    public static function getUserIdByAccess($accessToken, $accessSecret) {
        $homeAccess = HomeAccess::model()->findByAttributes(array(
            'user_access_token' => $accessToken,
            'user_access_secret' => $accessSecret,
        ));

        return $homeAccess != null ? $homeAccess->user_id : 0;
    }
}