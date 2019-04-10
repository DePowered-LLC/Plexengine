<?php
/*
@copy
 */

namespace pe\modules\System;
use pe\engine\DB;

class Notifications {
	public static function _add ($opts) {
		$is_sended = DB::find_first('notifications', [
			'user_id = :0: AND sender_id = :1: AND timestamp > :2:',
			'bind' => [$opts['user_id'], $_SESSION['userdata']['id'], time() - 3600]
		]);
		
		if (!$is_sended) {
			DB::insert('notifications', [
				'user_id' => $opts['user_id'],
				'sender_id' => $_SESSION['userdata']['id'],
				'info' => json_encode($opts['info']),
				'type' => $opts['type'],
				'timestamp' => time()
			]);
		}
	}

	public static function remove () {
		DB::delete('notifications', [
			'id = :0: AND user_id = :1:',
			'bind' => [$_GET['id'], $_SESSION['userdata']['id']]
		]);
	}
}