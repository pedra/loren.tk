<?php
//starting the autoloader classes (Autoloader)
set_include_path('.' . PATH_SEPARATOR . str_replace('phar:', 'phar|', PPHP)
        . trim(get_include_path(), ' .'));

//setting the automatic loading - Autoloader
spl_autoload_register(
        function ($class) {
            $class = ltrim('/' . strtolower(trim(strtr($class, '_\\', '//'), '/ ')), ' /\\') . '.php';
            $pth = explode(PATH_SEPARATOR, ltrim(get_include_path(), '.'));
            array_shift($pth);
            foreach ($pth as $f) {
                if (file_exists($f = str_replace('phar|', 'phar:', $f) . $class))
                    return require_once $f;
            }
        }
);
//include the autoloader Composer, if any.
if (file_exists(LIB . 'autoload.php')) include LIB . 'autoload.php';
