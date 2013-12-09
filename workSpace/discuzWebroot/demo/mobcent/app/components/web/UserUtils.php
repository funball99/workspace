<?php

/**
 * Utils about user
 *
 * @author  Xie Jianping <xiejianping@mobcent.com>
 */
class UserUtils {

    /**
     * 用户登陆状态
     */
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE_INVISIBLE = 1;
    const STATUS_ONLINE = 2;

    /**
     * 用户性别
     */
    const GENDER_SECRET = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    /**
     * get user's avatar
     * copy and modify by DISCUZ avatar function 
     *
     * @param int $uid
     * @param string $size
     * @return string
     */
    public static function getUserAvatar($uid, $size = 'middle') {
        global $_G;
        $ucenterurl = $_G['setting']['ucenterurl'];
        $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
        $uid = abs(intval($uid));

        return $ucenterurl . '/avatar.php?uid=' . $uid . '&size='. $size; 
    }

    public static function getUserName($uid)
    {
        $user = self::getUserInfo($uid);
        return !empty($user) ? $user['username'] : '';
    }

    public static function getUserGender($uid)
    {
        $user = self::getUserProfile($uid);
        return !empty($user) ? (int)$user['gender'] : self::GENDER_SECRET;
    }

    /**
     * get user's info
     * copy from DISCUZ getuserbyuid function 
     */
    public static function getUserInfo($uid)
    {
        return getuserbyuid($uid);
    }

    public static function getUserProfile($uid)
    {
        return DbUtils::getDiscuzCommand()
            ->select('*')
            ->from('{{common_member_profile}}')
            ->where('uid=:uid', array(':uid' => $uid))
            ->queryRow();
    }

    /**
     * 判断用户登陆状态
     * 
     * @param int $uid 用户id
     *
     * @return int 0为不在线, 1为隐身登陆, 2为在线登陆
     */
    public static function getUserLoginStatus($uid)
    {
    	$invisible = DbUtils::getDiscuzCommand()
    		->select('invisible')
    		->from('{{common_session}}')
    		->where('uid=:uid', array(':uid' => $uid))
    		->queryScalar();
        return $invisible !== false ? 
            ($invisible == 1 ? self::STATUS_ONLINE_INVISIBLE : self::STATUS_ONLINE) :
            self::STATUS_OFFLINE;
    }
    
    /**
     * 判断用户是否为好友
     *
     * @param int $uid 主用户id
     * @param int $fuid 要检测的用户id
     *
     * @return bool false为非好友, true为好友
     */
    public static function isFriend($uid , $fuid)
    {
    	$res = (int)DbUtils::getDiscuzCommand()
    		->select('COUNT(*)')
	    	->from('{{home_friend}}')
	    	->where('uid=:uid', array(':uid' => $uid))
	    	->andWhere('fuid=:fuid', array(':fuid' => $fuid))
	    	->queryScalar();
    	return $res !== 0;
    }
    
    /**
     * 判断用户是否在黑名单
     *
     * @param int $uid 主用户id
     * @param int $buid 要检测的用户id
     *
     * @return bool true为加入黑名单, false为没有加入黑名单
     */
    public static function isBlacklist($uid , $buid)
    {
    	$res = (int)DbUtils::getDiscuzCommand()
	    	->select('COUNT(*)')
	    	->from('{{home_blacklist}}')
	    	->where('uid=:uid', array(':uid' => $uid))
	    	->andWhere('buid=:buid', array(':buid' => $buid))
	    	->queryScalar();  
    	return $res !== 0;
    }

    /**
     * 判断该用户是否开启GPS定位功能
     */
    public static function isGPSLocationOn($uid)
    {
        return UserSetting::isGPSLocationOn($uid);
    }

    public static function checkAccess($accessToken, $accessSecret) {
        $res = array();
        if(empty($accessToken) || empty($accessSecret)) {
            //
        } else {
            $uid = HomeAccess::getUserIdByAccess($accessToken, $accessSecret);
            if ($uid > 0) {
                $res['uid'] = $uid;
            }
        }
        return !empty($res);
    }
}