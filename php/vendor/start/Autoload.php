<?php
//starting the autoloader classes (Autoloader)
set_include_path('.' . PATH_SEPARATOR . str_replace('phar:', 'phar|', PPHP)        
         . PATH_SEPARATOR . str_replace('phar:', 'phar|', VENDOR)
         . trim(get_include_path(), ' .'));

//setting the automatic loading - Autoloader
spl_autoload_register(
    function ($class) {
        $class = str_replace('\\', '/', trim($class, '\\'));
        //decode PSR-0
        $file = ($p = strrpos($class, '/')) 
            ? strtolower(substr($class, 0, $p)) . '/' . str_replace('_', '/', substr($class, $p + 1)) . '.php' 
            : $class . '.php';
        //get include path
        $pth = explode(PATH_SEPARATOR, ltrim(get_include_path(), '.'));
        array_shift($pth);
        //loop search
        foreach ($pth as $f) {
            if (file_exists($f = str_replace('phar|', 'phar:', $f) . $file))
                return require_once $f;
        }
    }
);

//include the autoloader Composer, if any.
if (file_exists(VENDOR . 'autoload.php')) include VENDOR . 'autoload.php';