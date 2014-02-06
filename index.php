<?php
/*
 * Loren API  
 *      by Bill Rocha [prbr@ymail.com]
 * 
 * Possivel graças a excelente classe LoremIpsumGenerator 
 *      de Renato Cassino [renatocassino@gmail.com]
 *      https://github.com/Tacno/populate-mysql-db
 * 
 * version 0.1 [ 2014.02.06.16.48.beta ]!
 * 
 */

//Buffer 
ob_start();

include 'LoremIpsumGenerator.php';
$loren = LoremIpsumGenerator::getInstance();

//Gera uma frase curta quando não tiver argumentos
if(!isset($_GET['lorenipsolum'])){
    $ret = $loren->generateByWords(7, true);
    goto output;
}

//Commands decoder
$url = explode('/', trim($_GET['lorenipsolum'], ' /'));

$cmd = (isset($url[0])) ? trim($url[0]) : 'r';
$qtd = (isset($url[1])) ? 0 + trim($url[1]) : 1;
$par = (isset($url[2])) ? 0 + trim($url[2]) : null;

//Help
$help = '<pre>Usage : http://loren.tk/ [COMAND] / [OPTION] / [OPTION]
Commands & options:
 
http://loren.tk/w/qtd       - generates words
http://loren.tk/c/min/max   - generates characters
http://loren.tk/p/qtd       - generates paragraphs
http://loren.tk/r/          - generates random words

http://loren.tk/            - generates 10 random words! (default)

<hr/>Given by <a href="https://github.com/Tacno"><b>Renato Cassino</b></a> and <a href="https://github.com/pedra"><b>Bill Rocha</b></a></pre>';

//Gerando
switch ($url[0]){
    case 'help':
        $ret = $help;
        break;
    case 'w':
        $ret = $loren->generateByWords($qtd);
        break;
    case 'c':
        $ret = $loren->generateByChars($qtd, $par);
        break;
    case 'p':
        $ret = $loren->generateByParagraph($qtd, $par);
        break;
    default :
        $ret = $loren->generateRandomWord();
}
    
//Gerando saída. . .
output:
exit($ret);
