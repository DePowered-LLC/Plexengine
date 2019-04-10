<?php
/*
@copy
 */

namespace pe\modules\Admin;
use pe\engine\DB;
use pe\engine\SystemEvents;
use pe\engine\View;
use pe\modules\System\Auth;

if(!Auth::is_access('admin')) View::error(403, 'No admin access');
class IndexRequests {
    public static function action () {
        if ($_POST == []) exit;
        $limit = [$_POST['action']];
        switch ($_POST['action']) {
            case 'kick':
                $limit[] = $_POST['reason'];
                break;
            case 'mute':
            case 'ban':
                $limit[] = $_POST['reason'];
                $limit[] = self::parseTime();
                break;
        }

        $is_guest = DB::find_first('guests', ['nick = :0:', 'bind' => [$_POST['nick']]]) != [];
        if (!$is_guest) {
            $is_user = DB::find_first('users', ['nick = :0:', 'bind' => [$_POST['nick']]]) != [];
            if (!$is_user) exit('no_account');
        }
        
        DB::update($is_guest ? 'guests' : 'users', [
            'limitation' => implode(';', $limit)
        ], [
            'nick = :0:',
            'bind' => [$_POST['nick']]
        ]);

        $info_msg = [
            'user_id' => 0,
            'nick' => $_SESSION['userdata']['nick'],
            'timestamp' => time(),
            'message' => implode(';', array_merge([$limit[0], $_POST['nick']], array_slice($limit, 1)))
        ];
        if (!SystemEvents::emit('send_message', $info_msg)) DB::insert('chat', $info_msg);
    }

    private static function parseTime () {
        $time = $_POST['time1'];
        switch ($_POST['time2']) {
            case 'h': $time *= 60; break;
            case 'd': $time *= 60 * 24; break;
            case 'w': $time *= 60 * 24 * 7; break;
            case 'mon': $time *= 60 * 24 * 30; break;
        }
        return time() + $time * 60;
    }

    public static function banlist () {
        $result = [];
        $banned = DB::find('users', ['limitation <> ""']);
        foreach ($banned as $key => $user) {
            $limitation = explode(';', $user['limitation']);
            if (isset($limitation[2]) && $limitation[2] <= time()) continue;
            unset($limitation[1]);
            $result[] = array_merge(['nick' => $user['nick']], $limitation);
        }
        exit(json_encode($result));
    }

    public static function unban () {
        DB::update('users', [
            'limitation' => ''
        ], [
            'nick = :0:',
            'bind' => [$_GET['nick']]
        ]);
    }

    public static function remove_msg () {
        $msg = SystemEvents::emit('get_message', $_GET['id']);
        if (!$msg) $msg = DB::find_first('chat', [
            'id = :0:',
            'bind' => [$_GET['id']]
        ]);

        if ($msg['user_id'] == 0) exit('sys');
        if (!SystemEvents::emit('remove_message', $_GET['id'])) {
            DB::delete('chat', [
                'id = :0:',
                'bind' => [$_GET['id']]
            ]);
        }

        $tag_msg = [
            'user_id' => 0,
            'nick' => '',
            'timestamp' => time(),
            'message' => 'remove;'.$_GET['id'],
            'color' => '#fff'
        ];
        if (!SystemEvents::emit('send_message', $tag_msg)) DB::insert('chat', $tag_msg);
    }
}
