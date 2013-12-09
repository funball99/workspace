<?php

define('IN_DISCUZ', true);
define('DISCUZ_ROOT', substr(dirname(__FILE__), 0, -29));

require_once(DISCUZ_ROOT . '/source/discuz_version.php');
require_once(DISCUZ_ROOT . '/config/config_ucenter.php');

require_once(sprintf('discuz_core_%s.php', MobcentDiscuz::getMobcentDiscuzVersion()));

class MobcentDiscuz {
    private static $_discuzVersions = array(
        'X2' => 'x20',
        'X2.5' => 'x25',
        'X3' => 'x30',
        'X3.1' => 'x30',
    );

    public static function getDiscuzVersions() {
        return self::$_discuzVersions;
    }

    public static function getDiscuzVersion() {
        return DISCUZ_VERSION;
    }

    public static function getMobcentDiscuzVersion()
    {
        if (isset(self::$_discuzVersions[self::getDiscuzVersion()]))
            return self::$_discuzVersions[self::getDiscuzVersion()];
        else {
            $version = end(self::$_discuzVersions);
            reset(self::$_discuzVersions);
            return $version;
        }
    }

    public static function getFuncNameWithVersion($funcName) {
        return sprintf("%s_%s", $funcName, self::getMobcentDiscuzVersion());
    }
}

C::creatapp();
C::app()->init();

runhooks();