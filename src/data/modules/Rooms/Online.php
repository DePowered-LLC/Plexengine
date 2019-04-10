<?php
/*
@copy
 */

namespace pe\modules\Rooms;
use pe\engine\DB;

class Online {
    public static function getList () {
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

    public static function onEnter () {
        if ($_SESSION['userdata']['id'] != -1) {
            DB::update('users', [
                'room_id' => $_SESSION['userdata']['room']
            ], [
                'id = :0:',
                'bind' => [$_SESSION['userdata']['id']]
            ]);
        }
    }

    public static function onLeave () {
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