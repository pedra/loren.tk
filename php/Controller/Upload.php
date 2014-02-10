<?php

namespace Controller;
use View;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of upload
 *
 * @author Bill Rocha <prbr@ymail.com at http://billrocha.tk>
 */
class Upload {
    
    
    function index(){
        exit((new View('upload'))->render(false));
        //p($_POST);
        
        
    }
    
    
    function file(){
        //p($_POST, false);
        print_r($_FILES); exit();
        
    }
    
}
