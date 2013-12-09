<?php

class NotifyListAction extends CAction {

	public function run($type = 'post', $page = 1, $pageSize = 20) {
		$res = WebUtils::initWebApiArray();
		$res = array_merge(array('rs' => 1, 'errcode' => 0), $res);

		$uid = $this->getController()->uid;

		$notifyInfo = $this->_getNotifyInfo($uid, $type, $page, $pageSize);
		$list = $notifyInfo['list'];
		$count = $notifyInfo['count'];
		
        $res = array_merge($res, WebUtils::getWebApiArrayWithPage_oldVersion(
            $page, $pageSize, $count));
		$res['list'] = $list;
        
		$transaction = Yii::app()->dbDz->beginTransaction();
		try {
			echo CJSON::encode($res);

			$this->_updateReadStatus($uid, $type);
			
			$transaction->commit();
		} catch(Exception $e) {
			var_dump($e);
		    $transaction->rollback();
		}

		Yii::app()->end();
	}

	private function _getNotifyInfo($uid, $type, $page, $pageSize) {
		$info = array(
			'count' => 0,
			'list' => array(),
		);

		$count = DzHomeNotification::getCountByUid($uid, $type);
		$notifyData = DzHomeNotification::getAllNotifyByUid($uid, $type, $page, $pageSize);
        
        foreach ($notifyData as $data) {
        	$matches = array();
        	preg_match_all('/&ptid=(\d+?)&pid=(\d+?)"/i', $data['note'], $matches);
        	$ptid = $matches[1][0];
            $pid = $matches[2][0];
        	$postInfo = $this->_getPostInfo($ptid, $pid);
        	if (!empty($postInfo)) {
	        	$info['list'][] = $postInfo;
        	} else {
        		--$count;
        	}
        }
		$info['count'] = $count;

		return $info;
	}

	private function _getPostInfo($ptid, $pid) {
		$info = array();

        $post = DzForumPost::getPostByTidAndPid($ptid, $pid);
        if (!empty($post)) {
            $forumName = ForumUtils::getForumName($post['fid']);
        	$threadPost = DzForumPost::getFirstPostByTid($ptid);

        	$info['board_name'] = $forumName;

        	$info['topic_id'] = (int)$ptid;
        	$info['topic_subject'] = $threadPost['subject'];
        	$info['topic_content'] = $threadPost['message'];
        	$info['topic_url'] = '';

        	$info['reply_content'] = $post['message'];
        	$info['reply_url'] = '';
        	$info['reply_remind_id'] = (int)$pid;
        	$info['reply_nick_name'] = $post['author'];
        	$info['user_id'] = (int)$post['authorid'];
        	$info['icon'] = UserUtils::getUserAvatar($post['authorid']);
        	$info['is_read'] = 1;
        	$info['replied_date'] = $post['dateline'] . '000';

        	$matches = array();
        	$message = $post['message'];
        	preg_match_all('/\[quote\](.+)\[\/quote\]\n(.+)/s', $message, $matches);
        	if (!empty($matches[0][0])) {
        		$matches[1][0] = preg_replace('/\[.+\]/', '', $matches[1][0]);
        		$info['topic_content'] = $matches[1][0];
	        	$info['reply_content'] = $matches[2][0];
        	}

        	$info['topic_content'] = $this->_filterNotifyMsg($info['topic_content']);
        	$info['reply_content'] = $this->_filterNotifyMsg($info['reply_content']);
        }

        return $info;
	}

    /**
     * copy from Discuz
     */
	private function _updateReadStatus($uid, $type) {
        // call_user_func(array($this, MobcentDiscuz::getFuncNameWithVersion(__FUNCTION__)), $uid, $type);
	    DzHomeNotification::updateReadStatus($uid);
        DbUtils::getDiscuzCommand()->update(
            '{{common_member}}',
            array('newprompt' => '0'),
            'uid=:uid',
            array(':uid' => $uid)
        );
    }

	private function _filterNotifyMsg($message) {
		$newMsg = preg_replace('/\[url.+?\[\/url\]/s', '', $message);
		$newMsg = preg_replace('/\[img\].+?\[\/img\]/s', '', $newMsg);
		$newMsg = preg_replace('/\[attach\].+?\[\/attach\]/s', '', $newMsg);
		// $newMsg = WebUtils::replaceLineMark($newMsg);
		$newMsg = WebUtils::subString($newMsg, 0, 140);
		// var_dump($newMsg);
		
		return $newMsg;
	}
}
