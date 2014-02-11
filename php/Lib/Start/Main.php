<?php
//Defines for CORE
defined('PPHP')     || define('PPHP', dirname(dirname(__DIR__)) . '/'); 
defined('LIB')      || define('LIB', PPHP . 'Lib/');

//Auxiliar Functions
include LIB.'Start/Autoload.php';
include LIB.'Start/Utils.php';

//Defines for template
defined('ROOT')     || define('ROOT', dirname($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME']) . '/');
defined('RPATH')    || define('RPATH', ((strpos(ROOT, 'phar://') === false) ? ROOT : str_replace('phar://', '', dirname(ROOT) . '/')));

//Defines for template to url access
$base = rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']), ' /');
defined('REQST')    || define('REQST', trim(str_replace($base, '', $_SERVER['REQUEST_URI']), ' /'));
defined('URL')      || define('URL', 'http://'.$_SERVER['SERVER_NAME'].$base.'/');

//Configurations
class_alias('Start\Config', 'o'); 
o::load(PPHP.'app.ini'); //load config ini file

//Template alias
class_alias('Start\Html\Doc', 'View');

//Decode route and instantiate controller
$controller = new Start\Controller(REQST, o::app('controller'));
$controller->setP404(false); //Page error404 enabled
$content = ($controller->solve() == false) ? (new View('404'))->render() : $controller->run();

//Template Engine
$out = new Start\Output($content);
$out->send();//produce and display HTML