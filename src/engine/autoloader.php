<?php
require_once 'view.php';
function loadClass($path) {
    $path = explode('\\', $path);
    if ($path[0] == 'pe') {
        switch ($path[1]) {
            case 'modules':
                $path = implode('/', array_slice($path, 2));
                $cDir = MODULES.'/'.$path;
                if (file_exists($cDir.'.php')) require_once($cDir.'.php');
                else View::error(500, 'Module class `'.$path.'` not found');
                break;
            case 'engine':
                $path = implode('/', array_slice($path, 2));
                $cDir = ENGINE.'/'.$path;
                if (file_exists($cDir.'.php')) require_once($cDir.'.php');
                else View::error(500, 'Engine class `'.$path.'` not found');
                break;
            default:
                View::error(500, 'Namespace `'.implode('\\', $path).'` not supported');
                break;
        }
    }
}

spl_autoload_register('loadClass');
