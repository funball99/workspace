<?php

/**
 * copy from discuz DISCUZ_ROOT/class/class_core.php, and do some modification
 * 
 * @author Xie Jianping <xiejianping@mobcent.com>
 */

class core
{
    private static $_tables;
    private static $_imports;
    private static $_app;
    private static $_memory;

    public static function app() {
        return self::$_app;
    }

    public static function creatapp() {
        if(!is_object(self::$_app)) {
            self::$_app = discuz_application::instance();
        }
        return self::$_app;
    }

    public static function t($name) {
        $pluginid = null;
        if($name[0] === '#') {
            list(, $pluginid, $name) = explode('#', $name);
        }
        $classname = 'table_'.$name;
        if(!isset(self::$_tables[$classname])) {
            if(!class_exists($classname, false)) {
                self::import(($pluginid ? 'plugin/'.$pluginid : 'class').'/table/'.$name);
            }
            self::$_tables[$classname] = new $classname;
        }
        return self::$_tables[$classname];
    }

    public static function memory() {
        if(!self::$_memory) {
            self::$_memory = new discuz_memory();
            self::$_memory->init(self::app()->config['memory']);
        }
        return self::$_memory;
    }

    public static function import($name, $folder = '', $force = true) {
        $key = $folder.$name;
        if(!isset(self::$_imports[$key])) {
            $path = DISCUZ_ROOT.'/source/'.$folder;
            if(strpos($name, '/') !== false) {
                $pre = basename(dirname($name));
                $filename = dirname($name).'/'.$pre.'_'.basename($name).'.php';
            } else {
                $filename = $name.'.php';
            }

            if(is_file($path.'/'.$filename)) {
                self::$_imports[$key] = true;
                return include $path.'/'.$filename;
            } elseif(!$force) {
                return false;
            } else {
                // leave for yii
                return true;
            }
        }
        return true;
    }

    public static function autoload($class) {
        $class = strtolower($class);
        if(strpos($class, '_') !== false) {
            list($folder) = explode('_', $class);
            $file = 'class/'.$folder.'/'.substr($class, strlen($folder) + 1);
        } else {
            $file = 'class/'.$class;
        }

        return self::import($file);
    }
}

Yii::registerAutoloader(array('core', 'autoload'));

class C extends core {}
class DB extends discuz_database {}