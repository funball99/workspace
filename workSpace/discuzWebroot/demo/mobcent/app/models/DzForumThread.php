<?php

class DzForumThread extends DiscuzAR
{
    const TYPE_VOTE = 1;

    // 主题图章
    const STAMP_MARROW = 0;
    const STAMP_HOT = 1;
    const STAMP_TOP = 4;
    
    // 主题图标
    const ICON_MARROW = 9;
    const ICON_HOT = 10;
    const ICON_TOP = 13;

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{forum_thread}}';
    }

    public function rules() {
        return array(
        );
    }

    // public function attributeLabels() {
    //  return array(
    //  );
    // }
    
    public static function getTopicByFid($fid, $page=1, $pageSize=10)
    {
        $topic = DbUtils::getDiscuzCommand()
            ->select('*')
            ->from('{{forum_thread}}')
            ->where('fid=:fid', array(':fid' => $fid))
            ->order('displayorder DESC')
            ->limit($pageSize, ($page-1)*$pageSize)
            ->queryAll();
        return $topic;
    }
    
    public static function getTopicByTid($tid)
    {
        $topic = DbUtils::getDiscuzCommand()
            ->select('*')
            ->from('{{forum_thread}}')
            ->where('tid=:tid', array(':tid' => $tid))
            ->queryRow();

        return $topic !== false ? $topic : array();
    }
    
    public static function getTopicCountByFid($fid)
    { 
        $topic = DbUtils::getDiscuzCommand()
        ->select('COUNT(*) as nums')
        ->from('{{forum_thread}}')
        ->where('fid=:fid', array(':fid' => $fid))
        ->queryRow();
        return $topic['nums'];
    }

    public static function getPostByTid($tid, $page=1, $pageSize=10)
    {
        $topic = DbUtils::getDiscuzCommand()
            ->select('*')
            ->from('{{forum_post}}')
            ->where('tid=:tid', array(':tid' => $tid))
            ->andWhere('first!=:first', array(':first' => '1'))
            ->order('pid ASC')
            ->limit($pageSize, ($page-1)*$pageSize)
            ->queryAll();
        return $topic;
    }
    
    public static function getPostCountByTid($tid)
    {
        $topic = DbUtils::getDiscuzCommand()
        ->select('COUNT(*) as nums')
        ->from('{{forum_post}}')
        ->where('tid=:tid', array(':tid' => $tid))
        ->andWhere('first!=:first', array(':first' => '1'))
        ->queryRow();
        return $topic['nums'];
    }
    
    public static function isVote($tid)
    {
        $topicInfo = self::_getTopicInfo($tid);
        return $topicInfo['special'] == self::TYPE_VOTE;
    }

    public static function isHot($tid)
    {
        $topicInfo = self::_getTopicInfo($tid);
        return $topicInfo['stamp'] == self::STAMP_HOT || $topicInfo['icon'] == self::ICON_HOT;
    }

    public static function isMarrow($tid)
    {
        $topicInfo = self::_getTopicInfo($tid);
        return $topicInfo['digest'] > 0 || $topicInfo['stamp'] == self::STAMP_MARROW || $topicInfo['icon'] == self::ICON_MARROW;
    }

    public static function isTop($tid)
    {
        $topicInfo = self::_getTopicInfo($tid);
        return $topicInfo['displayorder'] > 0 || $topicInfo['stamp'] == self::STAMP_TOP || $topicInfo['icon'] == self::ICON_TOP;
    }

    public static function _getTopicInfo($tid)
    {
        return is_numeric($tid) ? self::getTopicByTid($tid) : $tid;
    }
}