<?php

class PMListAction extends CAction {

	private $_pmReceivedList = array();

	public function run() {
		$res = WebUtils::initWebApiArray();
		$res = array_merge(array('rs' => 1, 'errcode' => 0), $res);

		$uid = $this->getController()->uid;

		// test
		// $_GET['pmlist'] ='{"head": {"errCode": 0, "errInfo": ""}, "body": {"pmInfos": [{"fromUid": "2", "startTime": "0", "stopTime": "0"}, {"fromUid": "3", "startTime": "0", "stopTime": "0"} ], "externInfo": {"onlyFromUid":0} } }';
		
		$res['body']['userInfo'] = $this->_getUserInfo($uid);
		$res['body']['pmList'] = $this->_getPMList($uid, $_GET['pmlist']);

		$transaction = Yii::app()->dbDzUc->beginTransaction();
		try {
			foreach ($this->_pmReceivedList as $pm) {
				PrivateMessageHeart::saveWithNewTime($pm);
			}

			echo WebUtils::jsonEncode($res);

			$transaction->commit();
		} catch(Exception $e) {
			var_dump($e);
		    $transaction->rollback();
		}

		Yii::app()->end();
	}

	private function _getUserInfo($uid) {
		$userInfo = array();

		$user = getuserbyuid($uid);
		if (!empty($user)) {
			$userInfo['uid'] = (int)$uid;
			$userInfo['name'] = $user['username'];
			$userInfo['avatar'] = UserUtils::getUserAvatar($uid, 'small');
		}

		return $userInfo;
	}

	private function _getPMList($uid, $pmlistJson) {
		$pmList = array();

		$pmInfos = CJSON::decode($pmlistJson);
		if (!empty($pmInfos)) {
			$externInfo = $pmInfos['body']['externInfo'];
			$isFilter = (isset($externInfo['onlyFromUid']) && $externInfo['onlyFromUid']);
			$pmInfos = $pmInfos['body']['pmInfos'];
			foreach ($pmInfos as $info) {
				$startTime = $info['startTime'] != 0 ? substr($info['startTime'], 0, -3) : 0;
				$stopTime = $info['stopTime'] != 0 ? substr($info['stopTime'], 0, -3) : 0;
				$userInfo = $this->_getUserInfo($info['fromUid']);
				$msgList = $this->_getPMMsgList($uid, $info['fromUid'], $startTime, $stopTime, $isFilter);
				
				$pmInfo['fromUid'] = (int)$userInfo['uid'];
				$pmInfo['name'] = $userInfo['name'];
				$pmInfo['avatar'] = $userInfo['avatar'];
				$pmInfo['msgList'] = $msgList;

				$pmList[] = $pmInfo;
			}
		}

		return $pmList;
	}

	private function _getPMMsgList($uid, $fromUid, $startTime, $stopTime = 0, $isFilter = false) {
		$msgList = array();

		$minMax = sprintf("%d_%d", min($uid, $fromUid), max($uid, $fromUid));
		$plid = DbUtils::getDzUCenterCommand()
            ->select('plid')
            ->from('{{pm_lists}}')
            ->where('min_max=:mm', array(':mm' => $minMax))
            ->queryScalar();

        if ($plid !== false) {
        	$command = DbUtils::getDzUCenterCommand()
	            ->select('*')
	            ->from(sprintf('{{%s}}', $this->_getPMMsgTableName($plid)))
	            ->where('delstatus=:ds', array(':ds' => 0))
	            ->andWhere('dateline>:startTime', array(':startTime' => $startTime));
	        if ($stopTime != 0) {
	        	$command->andWhere('dateline<:stopTime', array(':stopTime' => $stopTime));
	        }
	        $reader = $command->query();

	        while ($msg = $reader->read()) {
	        	if (!$isFilter || $msg['authorid'] != $uid) {
		        	$msgInfo['sender'] = (int)$msg['authorid'];
		        	$msgInfo['mid'] = (int)$msg['pmid'];
		        	$msgInfo['content'] = $this->_filterPMMsg($msg['message']);
		        	$msgInfo['type'] = 'text';
		        	$msgInfo['time'] = $msg['dateline'] . '000';

		        	$msgList[] = $msgInfo;
	        	}
	        }

	        if (!empty($msgList)) {
		        $this->_pmReceivedList[] = array(
	                'plid' => $plid,
	                'uid' => $uid,
	                'fromUid' => $fromUid,
		        	'time' => substr($msgList[count($msgList)-1]['time'], 0, -3),
		        );
	        }
        }

        return $msgList;
	}

    /**
     * copy by discuz
     */
	private function _getPMMsgTableName($plid) {
		$id = substr((string)$plid, -1, 1);
		return 'pm_messages_' . $id;
	}

	private function _filterPMMsg($message) {
		$newMsg = preg_replace('/\[url.+\[\/url\]\n/s', '', $message);
		$newMsg = preg_replace('/\[url\].+?\[\/url\]/s', '', $newMsg);
		$newMsg = preg_replace('/\[img\].+?\[\/img\]/s', '', $newMsg);
		// $newMsg = WebUtils::replaceLineMark($newMsg);
		$newMsg = WebUtils::subString($newMsg, 0, 140);
		// var_dump($newMsg);
		
		return $newMsg;
	}
}