<?php

namespace Start;
use Start\Db\Conn as DB;
use o;

class Output {
    
    //Parameters
    private $content = '';
    
    
    function __construct($content = '', $log = false){
        $this->content = $content;
        //Log in Db
        if($log) $this->logIn('', 'DB');        
    }
 
    /* SEND
     * Send headers & Output tris content
     * 
     */
    function send() {
        ob_end_clean();
        ob_start('ob_gzhandler');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        header('Cache-Control: must_revalidate, public, max-age=31536000');
        header('Server: START/1.3.0');//for safety ...
        header('X-Powered-By: START/1.3.0');//for safety ...
        exit($this->content . $this->statusBar());
    }
    
    
    /* DOWNLOAD
     * TODO: pleasy, make tests . . .
     *
     */
    function download($ext, $path){
        //search for mime type
        include o::app('config').'mimes.php';
        if (!isset($_mimes[$ext])) $mime = 'text/plain';
        else $mime = (is_array($_mimes[$ext])) ? $_mimes[$ext][0] : $_mimes[$ext];
        
        //get file
        $dt = file_get_contents($path);
        
        //download
        ob_end_clean();
        ob_start('ob_gzhandler');
    
        header('Vary: Accept-Language, Accept-Encoding');
        header('Content-Type: ' . $mime);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
        header('Cache-Control: must_revalidate, public, max-age=31536000');
        header('Content-Length: ' . strlen($dt));
        header('x-Server: nfw/RunPHP');
        header('ETAG: '.md5($path));
        exit($dt);
    }
    
    /* DEBUG/LOG
       * Save, send or display log string
       *
       * TODO: Lack build feature!
       *
       * @param $msg String Mesage
       * @param $mode String Save mode [log, mail, display]
       *
       * @return void
       */
    function logIn($msg, $mode = 'file') {
        if($mode == 'DB'){
            $db = new DB();
            $db->query("INSERT INTO ".o::log('table')." 
                        (IP, REQUEST, AGENT, USER)
                        VALUES ('".$_SERVER['REMOTE_ADDR']."', '".URL.REQST."', '".$_SERVER['HTTP_USER_AGENT']."', 0)");            
        } elseif($mode == 'file') file_put_contents(o::log('file'), date('YdmHis').' | '.$msg);
    }
    
    /* Status Bar
     * TODO : Criar o carregamento e compressão de arquivos CSS/JS para incluir os da barra de status.
     *
     * @return string Html status bar.
     */
    
    function statusBar(){
        if(!defined('INITIME')) return '';
	    $t = explode(' ',microtime());
        $i = explode(' ', INITIME);
	    return '<p style="position: fixed; bottom:0; right: 0; background: #999; color:#000; font-family: \'Oxygen Mono\', monospace; padding: 3px 5px; font-size: 10px; font-weight: normal; font-style: italic; text-shadow:1px 1px 1px #DDD">time: '.number_format((($t[0] * 1000)-$i[0] * 1000),1,',','.').' ms</p>';
    }
    
    function statusBar_old($extended = true){
        $sb = '<script type="text/javascript">var start_=\'none\';function starttatus(){if(start_==\'none\'){start_=\'block\'}else{start_=\'none\'};document.getElementById(\'starttatustable\').style.display=start_}</script>'
                . '<style>#starttatus{position:fixed;bottom:10px;right:10px;z-index:200;background:#777;background-color:rgba(60,60,60,0.7);'
                . 'box-shadow:0 1px 3px #000;cursor:pointer;font-size:10px;color:#FFF !important;'
                . 'font-family:Helvetica,Tahoma,monospace,\'Courier New\',Courier,serif;margin:0;'
                . 'padding:4px 8px;border:none;border-radius:7px;text-align:right}'
                . '#starttatustable{display:none;color:#FFF;margin:0 0 20px 0}'
                . '#starttatustable a{color:#FFF}'
                . '#starttatustable tr td{background:transparent !important;padding:2px 5px 0 0; color:#FF9}'
                . '#starttatustable th{padding:5px 0;color:#FFF}'
                . '#starttatustable tr th{font-size:12px;font-weight:bold}'
                . '#starttatustable pre {white-space:pre-wrap}'
                . '.starttatuslg td{border-bottom:1px dashed #999}'
                . '.r{text-align:right !important;padding:2px 0 0 0 !important}</style><div id="starttatus" onClick="starttatus()">';
    
        if($extended){
            $sb .= '<table id="starttatustable" title="hide!"><tr><th colspan="2"><a href="http://colabore.co.vu" target="new">ZoOm!</a></th></tr>';
            foreach(get_included_files() as $f){$sb .= '<tr><td colspan="2">'.$f.'</td></tr>';}
            $sb.='<tr><td>Files (total)</td><td class="r">'.count(get_included_files()).'</td></tr>'.
                 '<tr><td>Memory</td><td class="r">'.number_format(round((memory_get_usage()/1000),0),0,',','.').' kb</td></tr>'.
                 '<tr><td>Memory Peak</td><td class="r">'.number_format(round((memory_get_peak_usage()/1000),0),0,',','.').' kb</td></tr></table>';
        }
        $t = explode(' ',microtime());
        $i = explode(' ', INITIME);
        return $sb.number_format((($t[0] * 1000)-$i[0] * 1000),1,',','.').' ms</div>';
    }   
    
}
