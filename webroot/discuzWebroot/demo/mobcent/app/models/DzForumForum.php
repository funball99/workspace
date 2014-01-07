<?php

class DzForumForum extends DiscuzAR {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{forum_forum}}';
    }

    public function rules() {
        return array(
        );
    }

    // public function attributeLabels() {
    //  return array(
    //  );
    // }

    public static function getNameByFid($fid) {
        $criteria = new CDbCriteria(array(
            'select' => 'name',
            'condition' => 'fid=:fid',
            'params' => array(':fid' => $fid),
        ));
        $model = self::model()->find($criteria);
        return $model !== null ? $model->name : '';
    }
}