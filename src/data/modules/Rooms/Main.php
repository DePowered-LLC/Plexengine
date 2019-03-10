<?php
namespace pe\modules\Rooms;
use pe\engine\View;
use pe\engine\Router;
use pe\modules\System\Auth;

class Main {
    public function __construct () {
        Router::add('get', '/index', 'Main._selectRoom');
    }

    public static function _selectRoom () {
        return false;
        if (Auth::is_access('user')) View::load('room_select');
        else return false;
    }
}
