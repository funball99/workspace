<?php

class DzHomeNotification extends DiscuzAR {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{home_notification}}';
    }

    public function rules() {
        return array(
        );
    }

    // public function attributeLabels() {
    //  return array(
    //  );
    // }

    public static function getCountByUid($uid, $type, $isNew = null) {
        $command = DbUtils::getDiscuzCommand()
            ->select('COUNT(*)')
            ->from(self::model()->tableName())
            ->where('uid=:uid', array(':uid' => $uid))
            ->andWhere('type=:type', array(':type' => $type));
        if ($isNew !== null) {
            $command->andWhere('new=:new', array(':new' => $isNew));
        }
        return $command->queryScalar();
    }

    public static function getAllNotifyByUid($uid, $type, $page = 1, $pageSize = 10, $isNew = null) {
        $command = DbUtils::getDiscuzCommand()
            ->select('*')
            ->from(self::model()->tableName())
            ->where('uid=:uid', array(':uid' => $uid))
            ->andWhere('type=:type', array(':type' => $type));
        if ($isNew !== null) {
            $command->andWhere('new=:new', array(':new' => $isNew));
        }
        
        return $command->order('dateline DESC')
            ->limit($pageSize, ($page-1)*$pageSize)
            ->queryAll();
    }

    public static function updateReadStatus($uid) {
        $command = DbUtils::getDiscuzCommand();
        $command->update(
            self::model()->tableName(),
            array('new' => '0'),
            'uid=:uid AND new=:oldNew',
            array(':uid' => $uid, ':oldNew' => '1')
        );
    }
}