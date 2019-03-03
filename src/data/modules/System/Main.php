<?php
namespace pe\modules\System;
use pe\engine\Router;

class Main {
    public function __construct () {
        if (
            strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0')
         || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')
         || strpos($_SERVER['HTTP_USER_AGENT'], 'Edge')
         || strpos($_SERVER['HTTP_USER_AGENT'], 'Internet Explorer')
        ) {
            require_once ENGINE.'/errors/bad_browser.php';
            exit;
        }

        Router::add('get', '/public/{file:.*}', 'Resources.getPublic');
        Router::add('get', '/uploads/{type:.*}/{file:.*}', 'Resources.getUpload');
        Router::module('get', '/auth/<action>', 'Auth');
        Router::module('get', '/profile/<action>', 'Profile');
        Router::module('get', '/helper/<action>', 'Helper');
        Router::module('get', '/notifications/<action>', 'Notifications');
        Router::add('get', '/id{uid:-?[0-9]+}', 'Profile.view');
        Router::add('get', '/{path:.*}', 'Resources.loadView');
    }
}
