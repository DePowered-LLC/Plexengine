<?php
namespace pe\engine;
function loadClass($path) {
    $path = explode('\\', $path);
    if ($path[0] == 'pe') {
        switch ($path[1]) {
            case 'modules':
                $path = implode('/', array_slice($path, 2));
                $cDir = MODULES.'/'.$path;
                require_once ENGINE.'/View.php';
                if (file_exists($cDir.'.php')) require_once($cDir.'.php');
                else View::error(500, 'Module class `'.$path.'` not found');
                break;
            case 'engine':
                $path = implode('/', array_slice($path, 2));
                $cDir = ENGINE.'/'.$path;
                if (file_exists($cDir.'.php')) require_once($cDir.'.php');
                else exit($cDir.'.php 404');
                //else View::error(500, 'Engine class `'.$path.'` not found');
                break;
            default:
                View::error(500, 'Namespace `'.implode('\\', $path).'` not supported');
                break;
        }
    }
}

spl_autoload_register('pe\\engine\\loadClass');
