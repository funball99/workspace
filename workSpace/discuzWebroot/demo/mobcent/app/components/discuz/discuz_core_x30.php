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
        return self::_make_obj($name, 'table', DISCUZ_TABLE_EXTENDABLE);
    }

    public static function m($name) {
        $args = array();
        if(func_num_args() > 1) {
            $args = func_get_args();
            unset($args[0]);
        }
        return self::_make_obj($name, 'model', true, $args);
    }

    protected static function _make_obj($name, $type, $extendable = true, $p = array()) {
        $pluginid = null;
        if($name[0] === '#') {
            list(, $pluginid, $name) = explode('#', $name);
        }
        $cname = $type.'_'.$name;
        if(!isset(self::$_tables[$cname])) {
            if(!class_exists($cname, false)) {
                self::import(($pluginid ? 'plugin/'.$pluginid : 'class').'/'.$type.'/'.$name);
            }
            if($extendable) {
                self::$_tables[$cname] = new discuz_container();
                switch (count($p)) {
                    case 0: self::$_tables[$cname]->obj = new $cname();break;
                    case 1: self::$_tables[$cname]->obj = new $cname($p[1]);break;
                    case 2: self::$_tables[$cname]->obj = new $cname($p[1], $p[2]);break;
                    case 3: self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3]);break;
                    case 4: self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3], $p[4]);break;
                    case 5: self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3], $p[4], $p[5]);break;
                    default: $ref = new ReflectionClass($cname);self::$_tables[$cname]->obj = $ref->newInstanceArgs($p);unset($ref);break;
                }
            } else {
                self::$_tables[$cname] = new $cname();
            }
        }
        return self::$_tables[$cname];
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

        public static function analysisStart($name){
        $key = 'other';
        if($name[0] === '#') {
            list(, $key, $name) = explode('#', $name);
        }
        if(!isset($_ENV['analysis'])) {
            $_ENV['analysis'] = array();
        }
        if(!isset($_ENV['analysis'][$key])) {
            $_ENV['analysis'][$key] = array();
            $_ENV['analysis'][$key]['sum'] = 0;
        }
        $_ENV['analysis'][$key][$name]['start'] = microtime(TRUE);
        $_ENV['analysis'][$key][$name]['start_memory_get_usage'] = memory_get_usage();
        $_ENV['analysis'][$key][$name]['start_memory_get_real_usage'] = memory_get_usage(true);
        $_ENV['analysis'][$key][$name]['start_memory_get_peak_usage'] = memory_get_peak_usage();
        $_ENV['analysis'][$key][$name]['start_memory_get_peak_real_usage'] = memory_get_peak_usage(true);
    }

    public static function analysisStop($name) {
        $key = 'other';
        if($name[0] === '#') {
            list(, $key, $name) = explode('#', $name);
        }
        if(isset($_ENV['analysis'][$key][$name]['start'])) {
            $diff = round((microtime(TRUE) - $_ENV['analysis'][$key][$name]['start']) * 1000, 5);
            $_ENV['analysis'][$key][$name]['time'] = $diff;
            $_ENV['analysis'][$key]['sum'] = $_ENV['analysis'][$key]['sum'] + $diff;
            unset($_ENV['analysis'][$key][$name]['start']);
            $_ENV['analysis'][$key][$name]['stop_memory_get_usage'] = memory_get_usage();
            $_ENV['analysis'][$key][$name]['stop_memory_get_real_usage'] = memory_get_usage(true);
            $_ENV['analysis'][$key][$name]['stop_memory_get_peak_usage'] = memory_get_peak_usage();
            $_ENV['analysis'][$key][$name]['stop_memory_get_peak_real_usage'] = memory_get_peak_usage(true);
        }
        return $_ENV['analysis'][$key][$name];
    }
}

Yii::registerAutoloader(array('core', 'autoload'));

class C extends core {}
class DB extends discuz_database {}
