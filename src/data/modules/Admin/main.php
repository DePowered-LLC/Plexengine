<?php
/*
@copy
 */

load_db();

class Admin {
    public static function sys_router() {
        global $url_parts;
        if($url_parts[0] == 'admin') {
            require_once ENGINE.'/view.php';
            if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['access'] != 'admin') {
                View::error(403, 'No admin access');
                exit;
            }
            View::load('admin/index');
            exit;
        }
    }

    public static function router() {
        if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['access'] != 'admin') {
            View::error(404, 'No admin access');
            exit;
        }

        global $url_parts;
        self::{$url_parts[0]}();
    }

    public static function action() {
        if ($_POST == []) exit;
        $limit = [$_POST['action']];
        if ($_POST['action'] == 'kick') $limit[] = $_POST['reason'];
        if ($_POST['action'] == 'ban') {
            $limit[] = $_POST['reason'];
            $time = $_POST['time1'];
            switch ($_POST['time2']) {
                case 'h': $time *= 60; break;
                case 'd': $time *= 60 * 24; break;
                case 'w': $time *= 60 * 24 * 7; break;
                case 'mon': $time *= 60 * 24 * 30; break;
            }
            $limit[] = time() + $time * 60;
        }

        if ($_POST['action'] == 'kick' && !DB::find_first('users', ['nick = :0:', 'bind' => [$_POST['nick']]])) {
            DB::update('guests', [
                'kick' => $_POST['reason']
            ], [
                'nick = :0:',
                'bind' => [$_POST['nick']]
            ]);
        } else {
            DB::update('users', [
                'limitation' => implode(';', $limit)
            ], [
                'nick = :0:',
                'bind' => [$_POST['nick']]
            ]);
        }

        DB::insert('chat', [
            'user_id' => 0,
            'timestamp' => time(),
            'message' => implode(';', array_merge([$limit[0], $_POST['nick']], array_slice($limit, 1)))
        ]);
    }
}