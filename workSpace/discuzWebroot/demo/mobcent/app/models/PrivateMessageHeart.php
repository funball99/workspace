<?php

// class PrivateMessageHeart extends DiscuzUCenterAR {
class PrivateMessageHeart extends DiscuzAR {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
        // return '{{amy_pm_heart}}';
		return '{{ucenter_amy_pm_heart}}';
	}

	public function rules() {
		return array(
			array('plid, uid, from_uid, last_received', 'numerical', 'integerOnly'=>true),
		);
	}

	// public function attributeLabels() {
	// 	return array(
	// 	);
	// }

	public static function saveWithNewTime($pm) {
		$pmHeartModel = PrivateMessageHeart::model()->findByAttributes(array(
            'plid' => $pm['plid'],
            'uid' => $pm['uid'],
            'from_uid' => $pm['fromUid'],
        ));
        
        $isReceived = false;

        if (empty($pmHeartModel)) {
            $pmHeartModel = new PrivateMessageHeart;
            $pmHeartModel->attributes = array(
                'plid' => $pm['plid'],
                'uid' => $pm['uid'],
                'from_uid' => $pm['fromUid'],
                'last_received' => $pm['time'],
            );
            $pmHeartModel->insert();
        } else {
            if ($pm['time'] > $pmHeartModel['last_received']) {
                $pmHeartModel->last_received = $pm['time'];
                $pmHeartModel->update();
            } else {
                $isReceived = true;
            }
        }

        return $isReceived;
	}

    public static function testReceived($plid, $uid, $time) {
        $pmHeartModel = PrivateMessageHeart::model()->findByAttributes(array(
            'plid' => $plid,
            'uid' => $uid,
        ));
        return ($pmHeartModel !== null && $pmHeartModel->last_received >= $time);
    }
}