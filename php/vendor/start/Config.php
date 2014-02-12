<?php

namespace Start;

/* CONFIG ROOT STATIC CLASS
 * 
 * Use:
  1 - class_alias('Start\Config', 'o'); - basic usage
  2 - o::load('file.ini'); >> load ini file

  //by __callStatic emulate section named function
  3 - o::section();                           >> return indicate section array
  4 - o::section('index');                    >> return indicated section and index of array
  5 - o::section(array('index'=>'newValue')); >> add/mod specific section[index] = newValue
  6 - o::noSection();                         >> return false if section not exists
 *
 *
 */

class Config {

    //Parameters
    static $user = array();
    static $system = array();

    //Load config ini file
    static function load($file = null){
        if($file != null && file_exists($file))
                return static::$user = parse_ini_file($file,true);
    }

    //Set user parameter
    static function set($index,$val){
        return static::$user[$index] = $val;
    }

    //Get user parameter
    static function get($index){
        return isset(static::$user[$index]) ? static::$user[$index] : null;
    }

    //StaticCall
    static function __callStatic($name,$args){
        if(!isset(static::$user[$name])) return false;
        $st = static::$user[$name];

        //SET
        if(isset($args[0]) && is_array($args[0])){
            foreach($args[0] as $k=> $v){
                $st[$k] = $v;
            }
            static::$user[$name] = $st;
        }else{
            //GET
            foreach($args as $k=> $a){
                if(isset($st[$a])) $st = $st[$a];
            }
        }
        return $st;
    }

}
