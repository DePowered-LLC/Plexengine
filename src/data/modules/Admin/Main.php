<?php
/*
@copy
 */

namespace pe\modules\Admin;
use pe\engine\Router;
use pe\engine\View;
use pe\modules\System\Auth;

class Main {
    public function __construct () {
        Router::add('get', '/admin', 'Main.index');
        Router::module('get', '/admin/<action>', 'IndexRequests');
        Router::add('get', '/admin/{module:\w+}/{action:(-|\w)+}', 'Main.dispatchModule');
    }

    public static function index () {
        if(!Auth::is_access('admin')) View::error(403, 'No admin access');
        
        global $_MODULES;
        $menus = [];
        foreach ($_MODULES as $name => $module) {
            $info = json_decode(file_get_contents(MODULES.'/'.$name.'/info.json'), true);
            $module_name = self::getLocaleParam($info, 'name', $name.' Module');
            if (isset($info['menus'])) $menus[$name.'/index'] = $module_name;
        }
        
        View::load('admin.index', [
            'menus' => $menus
        ]);
    }

    public static function dispatchModule ($params) {
        if(!Auth::is_access('admin')) View::error(403, 'No admin access');
        $info = json_decode(file_get_contents(MODULES.'/'.$params['module'].'/info.json'), true);
        $menus = [];
        foreach ($info['menus'] as $key => $value) {
            $menus[$params['module'].'/'.$key] = $value['name'];
        }

        $common = [
            'menus' => $menus,
            'module_name' => self::getLocaleParam($info, 'name', $params['module'].' Module'),
            'module_id' => $params['module'],
            'module_version' => $info['version']
        ];

        if ($params['action'] == 'index') {
            View::load('admin.module.index', $common);
        } else {
            View::load('admin.module.edit', array_merge($common, [
                'action' => $info['menus'][$params['action']]
            ]));
        }
    }

    private static function getLocaleParam ($obj, $name, $fallback) {
        if (isset($obj[$name.'.'.$_COOKIE['lng']])) return $obj[$name.'.'.$_COOKIE['lng']];
        else if (isset($obj[$name.'.en'])) return $obj[$name.'.en'];
        else return $fallback;
    }
}
