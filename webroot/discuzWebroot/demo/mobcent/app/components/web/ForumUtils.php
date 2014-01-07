<?php

/**
 * 论坛相关工具类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 */
class ForumUtils
{
	/**
	 * about forum
	 */

	public static function getForumName($fid)
	{
		return DzForumForum::getNameByFid($fid);
	}

	/**
	 * about topic
	 */
	public static function getTopicList($fid)
	{
		return DzForumThread::getTopicByFid($fid);
	}
	
	public static function getTopicInfo($tid)
	{
		return DzForumThread::getTopicByTid($tid);
	}
	
	public static function getTopicCount($fid)
	{ 
		return DzForumThread::getTopicCountByFid($fid);
	}

    /**
     * 获取主题摘要(内容摘要以及图片)
     *
     * @return array array('msg' => '', 'image' => '') 
     */
	public static function getTopicSummary($tid)
	{
		$summary = array('msg' => '', 'image' => '');

		/*$post = self::getTopicPostInfo($tid);
		if (!empty($post)) {
			$post = DzForumPost::transPostContentToHtml($post);
			$msg = self::_filterPostMessageToPlain($post['message']);
		}
		
		$length = Yii::app()->params['mobcent']['forum']['topic']['summaryLength'];
		$summary['msg'] = WebUtils::subString($msg['main'], 0, $length);
		$summary['image'] = self::_filterPostMessageToImage($post['message']);
		*/
		return $summary;
	}

    /**
     * 测试是否投票主题
     * 
     * @param int|array $tid int为主题id, array为主题info
     *
     * @return bool
     */
	public static function isVoteTopic($tid)
	{
		return DzForumThread::isVote($tid);
	}

	public static function isHotTopic($tid)
	{
		return DzForumThread::isHot($tid);
	}

	public static function isMarrowTopic($tid)
	{
		return DzForumThread::isMarrow($tid);
	}
	
	public static function isTopTopic($tid)
	{
		return DzForumThread::isTop($tid);
	}

	/**
	 * about post
	 */
	

    /**
     * 获取主题帖信息
     */
	public static function getTopicPostInfo($tid)
	{
		return DzForumPost::getFirstPostByTid($tid);
	}

	public static function getPostInfo($tid)
	{
		return DzForumThread::getPostByTid($tid);
	}
	
	public static function getPostCount($tid)
	{
		return DzForumThread::getPostCountByTid($tid);
	}

    /**
     * 只保留帖子和引用的文本内容
     */
	private static function _filterPostMessageToPlain($postMsg)
	{
		$newPostMsg = array('main' => '', 'quote' => array('who' => '', 'msg' => ''));

		$postMsg = WebUtils::emptyReturnLine($postMsg);

		// 处理引用内容
		$matches = array();
		preg_match_all('/<blockquote><font.+?>(.*?)<\/font>.*?<br \/>(.*?)<\/blockquote>/', $postMsg, $matches);
		if (!empty($matches[1])) {
			$newPostMsg['quote']['who'] = preg_replace('/<.*?>/', '', $matches[1][0]);
		}
		if (!empty($matches[2])) {
			$tmpMsg = preg_replace('/<br \/>/s', "\n", $matches[2][0]);
			$newPostMsg['quote']['msg'] = preg_replace('/<.*?>/', '', $tmpMsg);	
		}
		$postMsg = preg_replace('/<blockquote>.*?<\/blockquote>/', '', $postMsg);

		// 处理处理附件
		$postMsg = preg_replace('/<ignore_js_op>.*?<\/ignore_js_op>/', ' ', $postMsg);
		
		$postMsg = preg_replace('/<br \/>/', "\n", $postMsg);
		$postMsg = preg_replace('/<.*?>/', '', $postMsg);
		$newPostMsg['main'] = $postMsg;

		return $newPostMsg;
	}

    /**
     * 只保留帖子内的一张图片
     */
	private static function _filterPostMessageToImage($postMsg)
	{
		$image = '';
		$postMsg = WebUtils::emptyReturnLine($postMsg);
		$matches = array();
		preg_match_all('/<img.*? zoomfile="(.+?)".*?\/>/', $postMsg, $matches);
		if (!empty($matches[1])) {
			if (strpos($matches[1][0], 'http') === 0)
				$image = $matches[1][0];
			else
				$image = Yii::app()->getController()->dzRootUrl . '/' . $matches[1][0];
		}

		return $image;
	}

	private static function _filterPostMessage($postMsg)
	{
		$msgArr = array();
		return $msgArr;
	}
}