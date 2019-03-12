<?php
namespace pe\modules\Rooms;
use pe\engine\View;
use pe\engine\DB;
use pe\engine\Router;
use pe\modules\System\Helper;
use pe\modules\System\Auth;

class Main {
    public function __construct () {
        Router::add('get', '/index', 'Main._select');
        Router::add('get', '/rooms/apply', 'Main.apply');
        Helper::on('load_messages', ['pe\\modules\\Rooms\\Main', '_load_messages']);
    }

    public static function _select () {
        DB::model('rooms', [
            'id' => [
                'type' => 'int(11)',
                'increment' => true,
                'unique' => true
            ],
            'name' => 'varchar(32)',
            'online' => 'int(11)',
            'online_limit' => 'int(11)'
        ]);

        if (Auth::is_access('user') && !isset($_SESSION['userdata']['room'])) {
            View::load('room_select');
        } else return false;
    }

    public static function apply () {
        if (Auth::is_access('user')) $_SESSION['userdata']['room'] = $_GET['id'];
    }

    public static function _load_messages ($time) {
        return [0];
    }
}
