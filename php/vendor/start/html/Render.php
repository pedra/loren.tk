<?php
/**
 * Template Render
 * @copyright           Bill Rocha - http://billrocha.tk
 * @license		http://billrocha/license
 * @author		Bill Rocha - prbr@ymail.com
 * @version		0.0.1
 * @package		Start\Html
 * @access 		public
 * @since		0.0.1
 */

namespace Start\HTML;

class Render {
    
    private $assets = null;

		
    /**
     * Renderiza o arquivo html.
     * Retorna um array com o produto da renderização ou 'ecoa' o resultado.
     *
     * @param bool $get	Retorna o produto da renderização para um pós-tratamento
     * @return array|void
    */
    function produce($php = true, $brade = true, $zTag = true){
        //With blade ???
        if($brade) $this->setContent($this->blade($this->getContent()));

        //With zTag ???
        if($zTag) {
            $ponteiro = -1;
            $content = $this->getContent();

            //Loop de varredura para o arquivo HTML
            while($ret = $this->zTag($content, $ponteiro)){
                $ponteiro = 0 + $ret['-final-'];
                $vartemp = '';
                
                //constant URL
                if($ret['-tipo-'] == 'var' && $ret['var'] == 'url') $vartemp = URL;
                elseif (method_exists($this, '_' . $ret['-tipo-'])) $vartemp = $this->{'_' . $ret['-tipo-']}($ret);

                //Incluindo o bloco gerado pelas zTags
                $content = substr_replace($this->getContent(), $vartemp, $ret['-inicio-'], $ret['-tamanho-']);
                $this->setContent($content);
                
                //RE-setando o ponteiro depois de adicionar os dados acima
                $ponteiro = strlen($vartemp) + $ret['-inicio-']; 
            }//end while
        }//end zTag

        //"Assessing" PHP contained in HTML
        if($php) $this->evalPHP();
        
        //returns the processed contents
        return $this->getContent();
    }


    /**
     * Scaner for zTag
     * Scans the file to find a ZTAG - returns an array with the data found Ztag
     *
     * @param string $arquivo	file content
     * @param string $ponteiro	file pointer
     * @param string $tag	Ztag to scan
     * @return array|false     array with the data found Ztag or false (not Ztag)
    */
    
    function zTag(&$arquivo, $ponteiro = -1, $tag = 'z:'){
        $inicio = strpos($arquivo, '<'.$tag, $ponteiro + 1);
        if($inicio !== false){ 
            //get the type (<z:tipo ... )
            $x = substr($arquivo, $inicio, 25);
            preg_match('/(?<tag>\w+):(?<type>\w+|[\:]\w+)/', $x, $m);
            if(!isset($m[0])) return false;           
            
            $ntag = $m[0];
            //the final ...
            $ftag = strpos($arquivo, '</' . $ntag . '>', $inicio);
            $fnTag   = strpos($arquivo, '/>', $inicio);
            $fn   = strpos($arquivo, '>', $inicio);            
            
            //not  /> or </z:xxx>  = error
            if($fnTag === false && $ftag === false) return false;
            
            if($ftag !== false ) {
                if($fn !== false && $fn < $ftag){
                    $a['-content-'] = substr($arquivo, $fn+1, ($ftag - $fn)-1);
                    $finTag = $fn;
                    $a['-final-'] = $ftag + strlen('</'.$ntag.'>');
                } else return false;  
            } elseif($fnTag !== false) {
                $a['-content-'] = '';
                $finTag = $fnTag;
                $a['-final-'] = $fnTag + 2;               
            } else return false;                    
            
            //catching attributes
            preg_match_all('/(?<att>\w+)="(?<val>.*?)"/', substr($arquivo, $inicio, $finTag - $inicio), $atb);
            
            if(isset($atb['att'])){
                foreach ($atb['att'] as $k=>$v){
                    $a[$v] = $atb['val'][$k];
                }
            }                   
            
            //block data
            $a['-inicio-'] = $inicio;
            $a['-tamanho-'] = ($a['-final-'] - $inicio);
            $a['-tipo-'] = 'var';
            
            if(strpos($m['type'], ':') !== false) $a['-tipo-'] = str_replace (':', '', $m['type']);
            else $a['var'] = $m['type'];
            
            return $a;
        }
        return false;
    }
    
    /**
     * Scaner para Blade.
     * Retorna o conteúdo substituindo variáveis BLADE (@var_name).
     *
     * @param string $arquivo	Conteúdo do arquivo a ser 'scaneado'
     * @return string           O memso conteudo com variáveis BLADE substituídas
    */
    function blade($arquivo){
        $t = strlen($arquivo) - 1;	
        $ini = '';
        $o = '';

        for($i =0; $i <= $t; $i++){

            if($ini != '' && $ini < $i){
                if($arquivo[$i] == '@' && ($i - $ini) < 2) {			
                    $o .= '@';
                    $ini = '';
                    continue;
                }
                if(!preg_match("/[a-zA-Z0-9\.:\[\]\-_()\/'$+,\\\]/",$arquivo[$i])){
                    $out1 = substr($arquivo, $ini+1, $i-$ini-1);
                    $out = rtrim($out1, ',.:');
                    $i += (strlen($out) - strlen($out1));

                    if($this->getVar($out)) $out = $this->getVar($out);				
                    else {
                        restore_error_handler();
                        ob_start();
                        $ret = eval('return '.$out.';');
                        if(ob_get_clean() === '') $out = $ret;
                        else $out = '';
                    }
                    $o .= $out; //exit($o);
                    $ini = '';
                    if($arquivo[$i] != ' ') $i --;//retirando espaço em branco...
                }
            } elseif($ini == '' && $arquivo[$i] == '@') $ini = $i;
              else $o .= $arquivo[$i];		
        }//end FOR
        return $o;
    }
    
    
    //################################ ZTAGS #######################################
 
    /**
     * evalPHP :: Rum PHP tag for contents.
     *
     * @param none
     * @return string 
    */
    function evalPHP() {
        extract($this->getVar());
        ob_start();
        eval('?>' . $this->getContent());

        //pegando o conteúdo processado
        $this->setContent(ob_get_contents());
        ob_end_clean();
    }
    
    /**
     * ClearData :: Clear all extra data.
     *
     * @param array $ret Starttag data array.
     * @return array Data array cleared.
    */
    function clearData($ret){
        unset($ret['var'], $ret['-inicio-'], $ret['-tamanho-'], $ret['-final-'], $ret['-tipo-'], $ret['-content-'], $ret['tag']);
        return $ret;
    }


    /**
     * _list :: Create ul html tag
     * Parameter "tag" is the list type indicator (ex.: <zoom:_list  . . . tag="li" />) 
     *
     * @param array $ret zTag data array
     * @return string|html
    */
    function _list($ret){
        $v = $this->getValues(trim($ret['var']));        
        if(!$v || !is_array($v)) return '';
        
        $tag = isset($ret['tag']) ? $ret['tag'] : 'li';
        $ret = $this->clearData($ret);
        
        //Tag UL and params. (class, id, etc)
        $o = '<ul';
        foreach($ret as $k=>$val){
            $o .= ' '.trim($k).'="'.trim($val).'"';            
        }
        
        //create list
        foreach ($v as $k=>$val){
            $o .= '<'.$tag.'>'.$val.'</'.$tag.'>';
        }
        return $o . '</ul>';
    }

    /**
     * zTAG :: Insere um elemento "select" 
     *
     * @param array $ret dados da zTag
     * @param string $nomeView Nome da view atual
     * @return string|html
    */
    function _select($ret, $nomeView){
            $vartemp = '';
            if(isset($ret['var'])){
                    if(isset($this->values[0][trim($ret['var'])])){$v=$this->values[0][trim($ret['var'])];}
                    else{$v='';}
                    if(is_string($nomeView)&&$this->values[$nomeView][trim($ret['var'])]!=''){$v=$this->values[$nomeView][trim($ret['var'])];}
                    unset($ret['var'],$ret['-inicio-'],$ret['-tamanho-'],$ret['-final-'],$ret['-tipo-'],$ret['conteudo']);
                    if($v!=''){
                            $ul='';
                            foreach($ret as $key=>$value){
                                    if($ul==''){$ul='<select';}
                                    if(trim($key)=='multiple'){$ul.=' '.trim($key);}else{$ul.=' '.trim($key).'="'.trim($value).'"';}
                                    unset($ret[$key]);
                            }
                            if($ul!=''){$vartemp=$ul.=">\n";}
                            else{$vartemp="<select>\n";}
                            foreach($v as $k=>$vl){
                                    $vartemp.='<option value="'.$k.'" ';
                                    if(is_array($vl)){$vartemp.=' selected="selected" >'.$vl[0]."</option>\n";}
                                    else{$vartemp.='>'.$vl."</option>\n";}
                            }
                            $vartemp.="</select>\n";
                    }
            }
            return $vartemp;
    }

    /**
     * zTAG :: Carregando um arquivo JavaScript
     *
     * @param array $ret dados do arquivo JS vindos da zTag
     * @return boll
    */
    function _script($ret){ 
        $ret = $this->clearData($ret);
        
        $as = (is_object($this->assets) ? $this->assets : $this->assets = new Assets());
        $link = $as->setScript($ret);
        
        //Montando a(s) tag
        $tag  = '';
        foreach ($link as $lk){
            $tag .= '<script type="text/javascript" src="'.o::url('script').$lk.'"></script>'."\n";
        }        
        return $tag;
    }

    /**
     * zTAG :: Carregando um arquivo CSS (na tag head)
     *
     * @param array $ret dados do arquivo CSS vindos da zTag
     * @return boll
    */
    function _style($ret){
        $ret = $this->clearData($ret);
        
        $as = (is_object($this->assets) ? $this->assets : $this->assets = new Assets());
        $link = $as->setStyle($ret);
        
        //Montando a(s) tag
        $tag  = '';
        foreach ($link as $lk){
            $tag .= '<link href="'.o::url('style').$lk.'"  rel="stylesheet" type="text/css"/>'."\n";
        }        
        return $tag;
    }

    /**
     * zTAG :: Carrega uma view pre-renderizada (subview)
     *
     * @param array $ret dados da zTag.
     * @return string   Content html or empty string.
    */
    function _view($ret){
        if(isset($ret['name'])){
            if($this->exists($ret['name'])) {
                return $this->getSubContent($ret['name']);
            }
        }
        return '';
    }

    /**
     * _var
     * Insert variable data assigned in view
     * Parameter "tag" is the tag type indicator (ex.: <zoom:variable  . . . tag="span" />) 
     *
     * @param array $ret zTag data array 
     * @return string   Renderized Html 
    */
    function _var($ret) {
        $v = $this->getVar(trim($ret['var']));    
        if(!$v) return '';
        
        //List type                
        if(is_array($v)) return $this->_list($ret);
        
        $tag = isset($ret['tag']) ? $ret['tag'] : 'span';
        $ret = $this->clearData($ret);
        
        //Var span (with class, id, etc);
        if(count($ret) > 0) {
            $d = '<'.$tag;
            foreach ($ret as $k=>$val){
                $d .= ' '.trim($k).'="'.trim($val).'"';
            }
            $v = $d.'>'.$v.'</'.$tag.'>';
        }
        return $v;
    }

    /**
     * Plugin / Module
     * Return renderized data for the indicated plugin or module
     *
     * @param array $ret zTag data
     * @return string|html Renderized content
    */
    final function _plugin($ret){return $this->_module($ret);}
    final function _module($ret){
            if(!isset($ret['name'])) return '';
            $module = '\\Module\\'.ucfirst($ret['name']).'\\Main';
            $module = new $module($ret);
            return $module->render();
    }
}