<?php

namespace Start;

class Controller {
    
    //Parameters
    private $url = '';
    private $mask = array();
    private $path = '';
    private $controller = 'Main';
    private $action = 'index';
    private $parms = array();
    private $p404 = false;
    
    
    function __construct($url = false, $path = false, $controller = false, $action = false){
        if($url != false) $this->url = $url;
		if($path != false) $this->path = $path;
		if($controller != false) $this->controller = $controller;
		if($action != false) $this->action = $action;
    }
    
    //get/set parameters
    function __call($nm, $arg){
            $nm = strtolower($nm);
            $arg = isset($arg[0]) ? $arg[0] : null;
            $func = substr($nm, 0, 3);
            $par = substr($nm, 3);

            //parameter exists?
            if(isset($this->$par)){
                    if($func == 'set') {
                        $this->$par = $arg;
                        return $this;
                    }
                    if($func == 'get') return $this->$par; 
            }
            return false;
    }   
    
    
    /* SOLVE
     * Resolve Controller & Action & Parms by friendly url
     *
     */
    function solve(){
        //breaking the url . . .
        $url = explode('/', trim($this->url, ' /').'/');
        
        //TODO: implement Mask function here!
        $url = $this->solveMask($url);
    
        //finding Controller -----------------------------------------
        if(isset($url[0]) && $url[0] != '' && file_exists($this->path . ucfirst($url[0]) . '.php')){
            $this->controller = ucfirst($url[0]);
            array_shift ($url);          
        }

        //new controller
        include $this->path . $this->controller.'.php';
        $this->controller = new $this->controller(); 
    
        //finding a action -----------------------------------------
        if(isset($url[0]) && $url[0] != '' && method_exists($this->controller, $url[0])) {
            $this->action = strtolower($url[0]);
            array_shift($url);
        }
        
        //collecting parameters ------------------------------------
        $this->parms = (!is_array($url) ? array() : $url);

        return $this;
    }
        
    
    /* RUN
     * Running the application front controller
     *
     */
    function run(){
        //run controller action and return -------------------------
        return call_user_func_array(array($this->controller, $this->action), $this->parms);
    }
    
    /* ADDMASK
     * Add mask in url decoder
     * TODO: not implemented for now (see function solve)
     */
    function addMask($mask, $result){
        $this->mask[$mask] = $result;
        return $this;
    }
    
    /* SOLVEMASK
     * 
     * TODO ALL!
     * 
     */
    private function solveMask($url){
        
        //TODO ?
        return $url;
    }

}