<?php

class HeartAction extends CAction {

    public function run() {
        $res = WebUtils::initWebApiArray();
        $res = array_merge(array('rs' => 1, 'errcode' => 0), $res);
        
        $uid = $this->getController()->uid;
        
        // get reply info
        $res['body']['replyInfo'] = $this->_getNotifyInfo($uid, 'post');

        // get @me info
        $res['body']['atMeInfo'] = $this->_getNotifyInfo($uid, 'at');

        // get private message that client unreceived
        $res['body']['pmInfos'] = $this->_getPmInfos($uid);

        $res['body']['externInfo']['heartPeriod'] = MessageController::HEART_PERIOD;

        echo CJSON::encode($res);
        Yii::app()->end();
    }

    private function _getNotifyInfo($uid, $type) {
        $info = array();

        $data = DbUtils::getDiscuzCommand()
            ->select('*')
            ->from('{{home_notification}}')
            ->where('uid=:uid', array(':uid' => $uid))
            ->andWhere('type=:type', array(':type' => $type))
            ->andWhere('new=:new', array(':new' => '1'))
            ->order('dateline DESC')
            ->queryAll();

        $info = array(
            'count' => count($data),
            'time' => !empty($data) ? $data[0]['dateline'] . '000' : "0",
        );

        return $info;
    }

    private function _getPmInfos($uid) {
        $pmInfos = array();

        // get all new pm
        $newPmList = DbUtils::getDzUCenterCommand()
            ->select('*')
            ->from('{{pm_members}} m')
            ->join('{{pm_lists}} l', 'm.plid=l.plid')
            ->where('uid=:uid', array(':uid' => $uid))
            ->andWhere('isnew=:isnew', array(':isnew' => '1'))
            ->queryAll();

        // get new pm unreceived
        foreach ($newPmList as $pm) {
            if (!PrivateMessageHeart::testReceived($pm['plid'], $uid, $pm['lastdateline'])) {
                $pmInfos[] = array(
                    'fromUid' => $this->_getFromUidByMinMax($uid, $pm['min_max']),
                    'time' => $pm['lastdateline'] . '000',
                );
            } 
        }

        return $pmInfos;
    }

    private function _getFromUidByMinMax($uid, $minMax) {
        $arr = explode('_', $minMax);
        $arr = array_diff($arr, array($uid));
        return (int)current($arr);
    }
}
