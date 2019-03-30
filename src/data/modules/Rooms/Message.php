<?php
namespace pe\modules\Rooms;
use pe\engine\DB;

class Message {
    public static function get ($info) {
        if (!is_array($info)) {
            $info = [
                'id = :0:',
                'bind' => [$info]
            ];
        }

        var_dump(['room'.$_SESSION['userdata']['room'].'_chat', $info]);
        return DB::find_first('room'.$_SESSION['userdata']['room'].'_chat', $info);
    }

    public static function getAll ($time) {
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

    public static function send ($data) {
        DB::insert('room'.$_SESSION['userdata']['room'].'_chat', $data);
        return true;
    }

    public static function remove ($info) {
        if (!is_array($info)) {
            $info = [
                'id = :0:',
                'bind' => [$info]
            ];
        }

        DB::delete('room'.$_SESSION['userdata']['room'].'_chat', $info);
        return true;
    }
}