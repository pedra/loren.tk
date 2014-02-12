<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Start\Db;

/**
 * Description of result
 *
 * @author Teo
 */
class Result {
    
    
    function __construct(){
    }
    
    /** GET
     * Get parameter value
     * @param string $parm
     * @return boolean
     */
    function get($parm){
        if(isset($this->$parm)) return $this->$parm;
        return false;        
    }
    
    /** SET
     * Set parameter
     * @param string|array $parm Name of parameter or array of parameter name and value
     * @param mixed $value Value of parameter
     * @return boolean
     */
    function set($parm, $value = null){
        if(is_array($parm)){
            foreach($parm as $k=>$v){ $this->$k = $v; }
            return true;
        }
        elseif(isset($this->$parm)) $this->$parm = $value;
        else return false;
    }

}
