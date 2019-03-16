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
        Helper::on('send_message', ['pe\\modules\\Rooms\\Main', '_send_message']);
        Helper::on('get_message', ['pe\\modules\\Rooms\\Main', '_get_message']);
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
}
