<?php

class PostListAction extends CAction
{
    public function run($topicId, $page=1, $pageSize=10)
    {
    	$res = WebUtils::initWebApiArray();
    	$res = array_merge(array('rs' => 1, 'errcode' => 0), $res);
    	
    	$count = ForumUtils::getPostCount((int)$topicId);
    	$N = ceil (($count-1) / $pageSize);
    	$res['total_num'] = (int)$count;
    	$res['page'] = (int)$page;
    	$res['has_next'] = ($page>=$N || $N==1) ?0:1;
    	
    	$topicArr = ForumUtils::getTopicInfo((int)$topicId);
    	//print_r($topicArr);exit;
    	$topicInfo = array();
    	$topicInfo['topic_id'] = (int)$topicArr['tid'];
    	$topicInfo['title'] = $topicArr['subject'];
    	$topicInfo['content'] = '';
    	$topicInfo['user_id'] = (int)$topicArr['authorid'];
    	$topicInfo['user_nick_name'] = $topicArr['author'];
    	$topicInfo['replies'] = (int)$topicArr['replies'];
    	$topicInfo['hits'] = (int)$topicArr['views'];
    	$topicInfo['status'] = (int)$topicArr['status'];
    	$topicInfo['essence'] = ForumUtils::isMarrowTopic($topicArr) ? 1 : 0;
    	$topicInfo['vote'] = ForumUtils::isVoteTopic($topicArr) ? 1 : 0;
    	$topicInfo['hot'] = ForumUtils::isHotTopic($topicArr) ? 1 : 0;
    	$topicInfo['top'] = ForumUtils::isTopTopic($topicArr) ? 1 : 0;
    	$topicInfo['create_date'] = $topicArr['dateline'] . '000';
    	if(empty($topicArr ['author']) && isset($topicArr['authorid']) && !empty($topicArr['authorid'])){
    		$topicInfo ['reply_status'] = (int)'-1 ';
    	}else if(empty($topicArr['author']) && empty($topicArr['authorid'])){
    		$topicInfo ['reply_status'] = (int)'0 ';
    	}else{
    		$topicInfo ['reply_status'] = (int)'1 ';
    	}
    	$topicInfo['icon'] = '';
    	$topicInfo['location'] = ''; 
    	$res['topic'] = $topicInfo;
    	
    	$postArr = ForumUtils::getPostInfo((int)$topicId);
    	//print_r($postArr);exit;
    	foreach($postArr as $post){
    		$postInfo['reply_id'] = (int)$topicId;
    		$postInfo['reply_content'] = '';
    		$postInfo['reply_name'] = $post['author'];
    		$postInfo['reply_posts_id'] = (int)$post['pid'];
    		if(empty($post ['author']) && isset($post['authorid']) && !empty($post['authorid'])){
    			$postInfo ['reply_status'] = -1;
    		}elseif(empty($post ['author']) && empty($post ['authorid'])){
    			$postInfo ['reply_status'] = 0;
    		}else{
    			$postInfo ['reply_status'] = 1;
    		}
    		$postInfo['status'] = (int)$post['status'];
    		$postInfo['position'] = $post['position'];
    		$postInfo['posts_date'] = $post['dateline']."000";
    		$postInfo['icon'] = '';
    		$postInfo['location'] = '';
    		
    		$postlist[] = $postInfo;
    	}
    	
    	$res['list'] = $postlist;
    	echo WebUtils::jsonEncode($res);
    	Yii::app()->end();
    }
}