<?php

/**
* UserSetting
*
* @uses     DiscuzAR
*
* @package  Application.Models
* @author Xie Jianping <xiejianping@mobcent.com>
* @license  
* @link     
*/
class UserSetting extends DiscuzAR
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{amy_user_setting}}';
    }

    public function rules()
    {
        return array(
            array('uid, ukey, uvalue', 'safe'),
        );
    }

    // public function attributeLabels() {
    //  return array(
    //  );
    // }

    /**
     * saveNewSetting
     * 
     * @param int $uid.
     * @param array $settings.
     * @param bool $return.
     *
     * @return bool|array.
     */
    public static function saveNewSettings($uid, $settings, $return = false)
    {
        // save new settings
        foreach ($settings as $key => $value) {
            $model = UserSetting::model()->findByAttributes(array(
                'uid' => $uid,
                'ukey' => $key,
            ));
            if ($model === null) {
                $model = new UserSetting;
                $model->attributes = array(
                    'uid' => $uid,
                    'ukey' => $key,
                    'uvalue' => $value,
                );
            } else 
                $model->uvalue = $value;

            $model->save();
        }

        if (!$return)
            return true;

        // return user settings
        $newSettings = array();
        $models = UserSetting::model()->findAllByAttributes(array('uid' => $uid));
        if (!empty($models)) {
            foreach ($models as $model) {
                $newSettings[] = array($model->ukey => $model->uvalue);
            }
        }

        return $newSettings;
    }

    public static function isGPSLocationOn($uid)
    {
        $model = self::model()->findByAttributes(array(
            'uid' => $uid,
            'ukey' => 'hidden',
        ));
        return !($model !== null && $model->uvalue == 1);
    }
}