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
use Start\Text\LoremIpsumGenerator as Lorem;

class Main {

function index($cmd = '', $qtd = 1, $par = null) {
    
    //Formatando . . .
    $cmd = trim($cmd);
    $qtd = 0 + trim($qtd);
    if($par != null) $par = 0 + trim($par);

    //limitando o gerador
    if($qtd > 10000) $qtd = 10000;
    if($par > 10000) $par = 10000;
    
    //Gerando
    switch ($cmd){
        case 'r': return Lorem::getInstance()->generateRandomWord($qtd);
        case 'w': return Lorem::getInstance()->generateByWords($qtd); break;
        case 'c': return Lorem::getInstance()->generateByChars($qtd, $par); break;
        case 'p': return Lorem::getInstance()->generateByParagraph($qtd, $par); break;  
        default : return (new View('index'))->render(); break;
    }
  }
}