<?php

/**
* SurroundingInfo
*
* @uses     DiscuzAR
*
* @package  Application.Models
* @author Xie Jianping <xiejianping@mobcent.com>
* @license  
* @link     
*/
class SurroundingInfo extends DiscuzAR
{
    const TYPE_USER = 1;
    const TYPE_POST = 2;
    const TYPE_TOPIC = 3;

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{home_surrounding_user}}';
    }

    public function rules()
    {
        return array(
        );
    }

    // public function attributeLabels() {
    //  return array(
    //  );
    // }

    public static function getUserCountByUid($uid, $longitude, $latitude, $radius)
    {
        $range = self::_getRange($longitude, $latitude, $radius);
        $command = DbUtils::getDiscuzCommand()
            ->select('COUNT(*)')
            ->from(self::model()->tableName())
            ->where('type=:type', array(':type' => self::TYPE_USER))
            ->andWhere('object_id!=:objectId', array(':objectId' => $uid));
        $command = self::_getRangeCommand($command, $range);

        return (int)$command->queryScalar();
    }

    public static function getAllUsersByUid($uid, $longitude, $latitude, $radius, 
                                            $page=1, $pageSize=10)
    {
        $range = self::_getRange($longitude, $latitude, $radius);
        $command = DbUtils::getDiscuzCommand()
            ->select('*, ' . self::_getSqlDistance($longitude, $latitude) . ' AS distance')
            ->from(self::model()->tableName())
            ->where('type=:type', array(':type' => self::TYPE_USER))
            ->andWhere('object_id!=:objectId', array(':objectId' => $uid));
        $command = self::_getRangeCommand($command, $range);
        $command->order('distance ASC')->limit($pageSize, ($page-1)*$pageSize);

        return $command->queryAll();
    }
    
    public static function getTopicCountByTid($longitude, $latitude, $radius)
    { 
        $range = self::_getRange($longitude, $latitude, $radius);
        $command = DbUtils::getDiscuzCommand()
            ->select('COUNT(*)')
            ->from(self::model()->tableName())
            ->where('type=:type', array(':type' => self::TYPE_TOPIC));
        $command = self::_getRangeCommand($command, $range);

        return (int)$command->queryScalar();
    }
    
    public static function getAllTopicsByTid($longitude, $latitude, $radius,
                                             $page=1, $pageSize=10)
    {
        $range = self::_getRange($longitude, $latitude, $radius);
        $command = DbUtils::getDiscuzCommand()
            ->select('*, ' . self::_getSqlDistance($longitude, $latitude) . ' AS distance')
            ->from(self::model()->tableName())
            ->where('type=:type', array(':type' => self::TYPE_TOPIC));
        $command = self::_getRangeCommand($command, $range);
        $command->order('distance ASC')->limit($pageSize, ($page-1)*$pageSize);

        return $command->queryAll();
    }
    
    private static function _getRange($longitude, $latitude, $radius)
    {
        $lgRange = $radius * 180 / (EARTH_RADIUS * M_PI);
        $ltRange = $lgRange / cos($latitude * M_PI / 180);
        
        $range['longitude']['max'] = $longitude + $lgRange;
        $range['longitude']['min'] = $longitude - $lgRange;
        $range['latitude']['max'] = $latitude + $ltRange;
        $range['latitude']['min'] = $latitude - $ltRange;

        return $range;
    }

    private static function _getSqlDistance($longitude, $latitude)
    {
        return sprintf('SQRT(POW((%f-longitude)/0.012*1023,2)+POW((%f-latitude)/0.009*1001,2))', $longitude, $latitude);
    }

    private static function _getRangeCommand($command, $range)
    {
        return $command
            ->andWhere('longitude BETWEEN :lgmin AND :lgmax', array(
                ':lgmin' => $range['longitude']['min'], 
                ':lgmax' => $range['longitude']['max'],
                ))
            ->andWhere('latitude BETWEEN :ltmin AND :ltmax', array(
                ':ltmin' => $range['latitude']['min'], 
                ':ltmax' => $range['latitude']['max'],
                ));
    }
}