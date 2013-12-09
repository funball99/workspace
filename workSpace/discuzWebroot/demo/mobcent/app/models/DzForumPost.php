<?php

class DzForumPost extends DiscuzAR {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{forum_post}}';
    }

    public function rules() {
        return array(
        );
    }

    // public function attributeLabels() {
    //  return array(
    //  );
    // }

    public static function getTableName($tid) {
        $dzVersion = MobcentDiscuz::getMobcentDiscuzVersion();
        $tableName = '';
        if ($dzVersion == 'x20') {
            $tableName = 'forum_post';
        } else {
            $tableName = C::t('forum_post')->get_tablename('tid:'.$tid);
        }
        
        return sprintf("{{%s}}", $tableName);
    }

    public static function getPostByTidAndPid($tid, $pid) {
        $post = DbUtils::getDiscuzCommand()
            ->select('*')
            ->from(self::getTableName($tid))
            ->where('pid=:pid', array(':pid' => $pid))
            ->queryRow();
 
        return $post !== false ? $post : array();
    }

    public static function getFirstPostByTid($tid) {
        $firstPost = DbUtils::getDiscuzCommand()
            ->select('*')
            ->from(self::getTableName($tid))
            ->where('tid=:tid', array(':tid' => $tid))
            ->andWhere('first=:first', array(':first' => 1))
            ->queryRow();

        return $firstPost !== false ? $firstPost : array();
    }

    public static function transPostContentToHtml($post)
    {
        $_GET['fid'] = $post['fid'];
        $_GET['tid'] = $post['tid'];

        $path = Yii::getPathOfAlias('application.components.discuz.forum');
        require_once(sprintf('%s/forum_viewthread_%s.php', $path, MobcentDiscuz::getMobcentDiscuzVersion()));

        $lastvisit = Yii::app()->params['discuz']['globals']['member']['lastvisit'];
        $ordertype = $maxposition = 0;
        $post = viewthread_procpost($post, $lastvisit, $ordertype, $maxposition);
        
        global $_G;
        $postlist[$post['pid']] = $post;
        if($_G['forum_attachpids'] && !defined('IN_ARCHIVER')) {
            require_once libfile('function/attachment');
            if(is_array($threadsortshow) && !empty($threadsortshow['sortaids'])) {
                $skipaids = $threadsortshow['sortaids'];
            }
            parseattach($_G['forum_attachpids'], $_G['forum_attachtags'], $postlist, $skipaids);
        }

        if(empty($postlist)) {
            showmessage('post_not_found');
        } elseif(!defined('IN_MOBILE_API')) {
            foreach($postlist as $pid => $post) {
                $postlist[$pid]['message'] = preg_replace("/\[attach\]\d+\[\/attach\]/i", '', $postlist[$pid]['message']);
            }
        }

        return $post;
    }
}