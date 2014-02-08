<?php

namespace Lib\Start;

class Controller {
    
    //Parameters
    private $url = '';
    private $mask = array();
    private $path = '';
    private $controller = 'main';
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
    function solve() {
        //breaking the url . . .
        $url = explode('/', trim($this->url, ' /').'/');
        
        //TODO: implement Mask function here!
    
        //finding a controller -------------------------------------
        $controller = strtolower((isset($url[0]) && $url[0] != '') ? $url[0] : $this->controller); //default
        $action = true; 
                
        //passing control to the controller class
        $pathCtrl = $this->path . $controller . '.php';
        if (file_exists($pathCtrl)) {
             include $pathCtrl;
             if (isset($url[0]) && $controller == $url[0]) array_shift($url);
        } elseif($this->p404) {
            return false;   
        } elseif (file_exists($this->path . $this->controller . '.php')) {
            include $this->path . $this->controller . '.php';
            $controller = $this->controller;
            $action = false;
        } else return false;

        //new controller
        $controller = ucfirst($controller);
        $this->controller = new $controller(); 
    
        //finding a action -----------------------------------------
        if($action == true 
           && isset($url[0]) 
           && $url[0] != '' 
           && method_exists($controller, $url[0])) $this->action = strtolower($url[0]);
        if(isset($url[0]) && $this->action == strtolower($url[0])) array_shift($url);
        
        //collecting parameters ------------------------------------
        if (!is_array($url)) $url = Array();
        $this->parms = $url;

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

}