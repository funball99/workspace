<?php

class TopicListAction extends CAction
{
    public function run($boardId,$page=1, $pageSize=10)
    {
    	$res = WebUtils::initWebApiArray();
    	$res = array_merge(array('rs' => 1, 'errcode' => 0), $res); 

    	$res['board_category_id'] = (int)$boardId;
    	$res['board_category_name'] = ForumUtils::getForumName((int)$boardId);
    	$res['board_category_type'] = 2;
    	$res['classificationTop_list'] = array();
    	$res['classificationType_list'] = array();
    	$res['total_num'] = ForumUtils::getTopicCount((int)$boardId);
    	$res['page'] = (int)$page;
    	 
    	$topicArr = ForumUtils::getTopicList((int)$boardId);
    	$list = array();
    	foreach($topicArr as $topic){
    		$tmpTopicInfo = ForumUtils::getTopicInfo((int)$topic['tid']);
    		$TopicSummary = ForumUtils::getTopicSummary((int)$topic['tid']);
    		$topicInfo['board_id'] = (int)$boardId;
    		$topicInfo['board_name'] = ForumUtils::getForumName((int)$boardId);
    		$topicInfo['topic_id'] = (int)$topic['tid'];
    		$topicInfo['title'] = $topic['subject'];
    		$topicInfo['user_id'] = (int)$topic['authorid'];
    		$topicInfo['user_nick_name'] = $topic['author'];
    		$topicInfo['last_reply_date'] = $topic['lastpost'] . '000';
    		$topicInfo['vote'] = ForumUtils::isVoteTopic($tmpTopicInfo) ? 1 : 0;
    		$topicInfo['hot'] = ForumUtils::isHotTopic($tmpTopicInfo) ? 1 : 0;
    		$topicInfo['hits'] = (int)$topic['views'];
    		$topicInfo['replies'] = (int)$topic['replies'];
    		$topicInfo['essence'] = ForumUtils::isMarrowTopic($tmpTopicInfo) ? 1 : 0;
    		$topicInfo['top'] = ForumUtils::isTopTopic($tmpTopicInfo) ? 1 : 0;
    		$topicInfo['subject'] = $TopicSummary['msg'];
    		$topicInfo['pic_path'] = $TopicSummary['image'];;
    		$list[] = $topicInfo;
    		
    	}
    	$res['list'] = $list;
    	echo WebUtils::jsonEncode($res);
    	Yii::app()->end();
    	
    }
}