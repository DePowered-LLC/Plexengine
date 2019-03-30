<?php
/*
@copy
 */

namespace pe\modules\Admin;
use pe\engine\Router;
use pe\engine\View;
use pe\engine\DB;
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
        if (isset($_GET['apply'])) exit(self::applyModule($params));

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
            $menu_info = $info['menus'][$params['action']];
            $vars = array_merge($common, [
                'menu_info' => $menu_info
            ]);

            switch ($menu_info['type']) {
                case 'db.view':
                    $vars['data'] = DB::find($menu_info['model']);
                    $vars['scheme'] = json_decode(file_get_contents(MODULES.'/'.$params['module'].'/models/'.$menu_info['model'].'.json'), true);
                    break;
                case 'db.add':
                    $vars['scheme'] = json_decode(file_get_contents(MODULES.'/'.$params['module'].'/models/'.$menu_info['model'].'.json'), true);
                    break;
                default:
                    View::error(500, 'Page type `'.$menu_info['type'].'` not found.');
            }

            View::load('admin.module.'.$menu_info['type'], $vars);
        }
    }

    public static function applyModule ($params) {
        $info = json_decode(file_get_contents(MODULES.'/'.$params['module'].'/info.json'), true);
        $menu_info = $info['menus'][$params['action']];
        switch ($menu_info['type']) {
            case 'db.add':
                foreach ($_POST as $key => $val) {
                    if (!$val || is_string($val) && trim($val) == '') exit('fill-all');
                }

                DB::insert($menu_info['model'], $_POST);
                exit('success');
            case 'db.view':
                switch ($_GET['apply']) {
                    case 'remove':
                        DB::delete($menu_info['model'], [
                            'id = :0:',
                            'bind' => [$_POST['id']]
                        ]);
                        exit('success');
                }
                break;
            default:
                View::error(500, 'Page type `'.$menu_info['type'].'` not available for POST or not found.');
        }
    }

    public static function getLocaleParam ($obj, $name, $fallback) {
        if (isset($obj[$name.'.'.$_COOKIE['lng']])) return $obj[$name.'.'.$_COOKIE['lng']];
        else if (isset($obj[$name.'.en'])) return $obj[$name.'.en'];
        else return $fallback;
    }
}
