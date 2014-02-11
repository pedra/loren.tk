<?php
/**
 * Template Assets
 * @copyright           Bill Rocha - http://billrocha.tk
 * @license		http://billrocha.tk/license
 * @author		Bill Rocha - prbr@ymail.com
 * @version		0.0.1
 * @package		Start\Html
 * @access 		public
 * @since		0.0.1
 */

namespace Start\Html;
use o;

class Assets {    
    
    //Max cache time
    private $lifeTime = 100;
    
    
    /* setStyle
     * Modeling the assets output
     * ZoomTag format: <z:_style file="main, lib/reset" cached="true" />
     *
     */
    function setStyle($ztag){
        //Se não for indicado 'file' ...
        if(!isset($ztag['file'])) return false;
        $cached = (isset($ztag['cached']) && trim($ztag['cached']) == 'true') ? true : false;        
        
        $tmp = explode(',', ','.trim($ztag['file'], ' /'));
        foreach($tmp as $i){
            $files[] = trim($i, ' /');
        }
        
        $cache = md5(str_replace(array('|','/'), '_', implode('_', $files))).'.cache.css';

        //create cache file (if not exists)
        if($cached){
            if(!file_exists(o::html('style').$cache) || (time() - filemtime(o::html('style').$cache)) > $this->lifeTime) {            
                $dt = '';
                foreach ($files as $file) {
                    if(file_exists(o::html('style').trim($file).'.css'))
                        $dt .= preg_replace("#/\*[^*]*\*+(?:[^/*][^*]*\*+)*/#","",
                               preg_replace('<\s*([@{}:;,]|\)\s|\s\()\s*>S','\1',
                               str_replace(array("\n","\r","\t"),'',
                               file_get_contents(o::html('style').trim($file).'.css'))))."\n\n";
                }    
                file_put_contents(o::html('style').$cache, $dt);                
            }
            $out = array($cache);
        } else {            
           foreach ($files as $file) {
                if(file_exists(o::html('style').trim($file).'.css')) $out[] = trim($file).'.css';
            }
        }        
        return $out;
    }
    
    /* setScript
     * Modeling the assets output
     * ZoomTag format: <z:_script file="main, lib/jquery/jquery-ui" cached="true" />
     *
     */
    function setScript($ztag){
        //Se não for indicado 'file' ...
        if(!isset($ztag['file'])) return false;
        $cached = (isset($ztag['cached']) && trim($ztag['cached']) == 'true') ? true : false;        
        
        $tmp = explode(',', ','.trim($ztag['file'], ' /'));
        foreach($tmp as $i){
            $files[] = trim($i, ' /');
        }
        
        $cache = md5(str_replace(array('|','/'), '_', implode('_', $files))).'.cache.js';

        //create cache file (if not exists)
        if($cached){
            if(!file_exists(o::html('script').$cache) || (time() - filemtime(o::html('script').$cache)) > $this->lifeTime) {          
                $dt = '';
                foreach ($files as $file) {
                    if(file_exists(o::html('script').$file.'.js'))
                        $dt .=  preg_replace("#/\*[^*]*\*+(?:[^/*][^*]*\*+)*/#","",
                                preg_replace("/^\s/m",'',
                                str_replace("\t",'',
                                file_get_contents(o::html('script').$file.'.js'))))."\n\n";
                }    
                file_put_contents(o::html('script').$cache, $dt);                
            }
            $out = array($cache);
        } else {            
           foreach ($files as $file) {
                if(file_exists(o::html('script').$file.'.js')) $out[] = $file.'.js';
            }
        }        
        return $out;
    }
    
    
    /* setLifeTime
     * Max cache time
     */
    function setLifeTime(int $time){
        return $this->lifeTime = $time;
    }
    
    /* getLifeTime
     * Max cache time
     */
    function getLifeTime(){
        return $this->lifeTime;
    }

}
