<?php
/*
@copy
 */

namespace pe\modules\Rooms;
use pe\engine\DB;
use pe\engine\Router;
use pe\engine\SystemEvents;
use pe\engine\View;
use pe\modules\System\Auth;

class Main {
    public function __construct () {
        Router::add('get', '/index', 'Main.select');
        Router::add('get', '/rooms/select', 'Main.select', false, true);
        Router::add('get', '/rooms/apply', 'Main.apply');

        SystemEvents::on('load_messages', ['pe\\modules\\Rooms\\Main', '_load_messages']);
        SystemEvents::on('send_message', ['pe\\modules\\Rooms\\Main', '_send_message']);
        SystemEvents::on('get_message', ['pe\\modules\\Rooms\\Main', '_get_message']);

        SystemEvents::on('get_online', ['pe\\modules\\Rooms\\Main', '_get_online']);
        SystemEvents::on('enter', ['pe\\modules\\Rooms\\Main', '_enter']);
        SystemEvents::on('leave', ['pe\\modules\\Rooms\\Main', '_leave']);
    }

    public static function install () {
        DB::model('rooms', 'Rooms');
        if (!DB::count('rooms', 'id')) {
            DB::insert('rooms', [
                'name' => 'Main',
                'online_limit' => 50
            ]);

            DB::insert('rooms', [
                'name' => 'Garden',
                'online_limit' => 50
            ]);
        }

        DB::modelExtend('users', [
            'room_id' => 'int(11)'
        ]);
    }

    public static function select ($params, $ignore = false) { 
        self::install();       
        if (Auth::is_access('user') && ($ignore || !isset($_SESSION['userdata']['room']))) {
            $rooms = DB::find('rooms');
            $rooms_online = DB::query('SELECT `room_id` as `id`, COUNT(`room_id`) as `online` FROM `users` WHERE `last_online` >= '.(time() - 4).' GROUP BY `room_id`', true);
            $total_online = 0;
            foreach ($rooms as $key => $room) {
                $rooms[$key]['online'] = 0;
                foreach ($rooms_online as $room_info) {
                    if ($room_info['id'] == $room['id']) {
                        $rooms[$key]['online'] = $room_info['online'];
                        $total_online += $room_info['online'];
                        break;
                    }
                }
            }

            View::load('room_select', [
                'rooms' => $rooms,
                'total_online' => $total_online
            ]);
        } else return false;
    }

    public static function apply () {
        if (Auth::is_access('user')) {
            if ($_GET['id'] == -1) {
                unset($_SESSION['userdata']['room']);
                exit;
            }

            $room = DB::find_first('rooms', [
                'id = :0:',
                'bind' => [$_GET['id']]
            ]);

            if (!$room) exit('no_room');
            $online = DB::count('users', 'room_id', [
                'room_id = :0: AND last_online >= :1:',
                'bind' => [$_GET['id'], time() - 4]
            ]);

            if ($online >= $room['online_limit']) exit('refulled');            
            $_SESSION['userdata']['room_name'] = $room['name'];
            $_SESSION['userdata']['room'] = $_GET['id'];
        }
    }

    public static function _load_messages ($time) {
        $chat_table = 'room'.$_SESSION['userdata']['room'].'_chat';
        DB::model($chat_table, [
            'id' => [
                'type' => 'int(11)',
                'increment' => true,
                'unique' => true
            ],
            'user_id' => 'int(11)',
            'nick' => 'varchar(32)',
            'message' => 'text',
            'timestamp' => 'bigint(20)',
            'color' => 'varchar(14)'
        ]);

        global $_CONFIG;
        if ($time != -1) {
            return DB::find($chat_table, [
                'timestamp >= :0: ORDER BY id DESC',
                'bind' => [time() - $time - 1]
            ]);
        } else {
            return DB::find($chat_table, 'ORDER BY id DESC LIMIT 0,'.$_CONFIG['messages_limit']);
        }
    }

    public static function _send_message ($data) {
        DB::insert('room'.$_SESSION['userdata']['room'].'_chat', $data);
        return true;
    }

    public static function _get_message ($info) {
        if (!is_array($info)) {
            $info = [
                'id = :0:',
                'bind' => [$info]
            ];
        }

        return DB::find_first('room'.$_SESSION['userdata']['room'].'_chat', $info);
    }

    public static function _get_online () {
        $result = [];
        $result['users'] = DB::find('users', [
            'last_online > :0: AND room_id = :1:',
            'bind' => [time() - 4, $_SESSION['userdata']['room']]
        ]);

        $result['guests'] = DB::find('guests', [
            'last_online > :0:',
            'bind' => [time() - 4]
        ]);
        return $result;
    }

    public static function _enter () {
        if ($_SESSION['userdata']['id'] != -1) {
            DB::update('users', [
                'room_id' => $_SESSION['userdata']['room']
            ], [
                'id = :0:',
                'bind' => [$_SESSION['userdata']['id']]
            ]);
        }
    }

    public static function _leave () {
        if ($_SESSION['userdata']['id'] != -1) {
            DB::update('users', [
                'room_id' => 0
            ], [
                'id = :0:',
                'bind' => [$_SESSION['userdata']['id']]
            ]);
        }
    }

}
