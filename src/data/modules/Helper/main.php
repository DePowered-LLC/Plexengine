<?php
load_db();

class Helper {
	public static function router() {
		global $url_parts;
		self::{$url_parts[0]}();
	}

	public static function load_data() {
		$data = [];
		if (!isset($_SESSION['userdata'])) {
			View::error(403, 'No read access');
			exit;
		}

		$user_selector = [
			'id = :0:',
			'bind' => [$_SESSION['userdata']['id']]
		];

		if ($_SESSION['userdata']['id'] != -1) {
			$limitation = DB::find_first('users', $user_selector);
			$limitation = $limitation['limitation'];
			$limitation = explode(';', $limitation);
			switch ($limitation[0]) {
				case 'kick':
					session_destroy();
					DB::update('users', [
						'limitation' => ''
					], $user_selector);
					break;
				case 'ban':
					if ($limitation[2] <= time()) {
						DB::update('users', [
							'last_online' => time() - 3,
							'limitation' => ''
						], $user_selector);
						break;
					} else {
						session_destroy();
						exit(json_encode([
							'limitation' => $limitation
						]));
					}
			}
			$data['limitation'] = $limitation;
		} else {
			$guest_selector = [
				'nick = :0:',
				'bind' => [$_SESSION['userdata']['nick']]
			];

			$guest = DB::find_first('guests', $guest_selector);
			if ($guest) {
				if ($guest['kick'] != '') {
					DB::delete('guests', $guest_selector);
					session_destroy();
					exit(json_encode([
						'limitation' => ['kick', $guest['kick']]
					]));
				}
			}
		}

		if(isset($_GET['t']) && $_GET['t'] > 0) {
			$data['msgs'] = DB::find('chat', [
				'timestamp >= :0: ORDER BY id DESC',
				'bind' => [time() - intval($_GET['t']) - 1]
			]);
		} else {
			global $_CONFIG;
			self::spy_msg('enter');
			$data['msgs'] = DB::find('chat', 'ORDER BY id DESC LIMIT 0,'.$_CONFIG['messages_limit']);

			// Get smile packs
			$data['smiles'] = [];
			$smile_packs = scandir(DATA.'/smiles');
			array_shift($smile_packs);
			array_shift($smile_packs);
			foreach($smile_packs as $pack_id) {
				$data['smiles'][$pack_id] = [];
				$smile_list = scandir(DATA.'/smiles/'.$pack_id);
				array_shift($smile_list);
				array_shift($smile_list);
				foreach($smile_list as $smile_id) {
					if($smile_id == 'icon.png') continue;
					$smile_id = str_replace('.png', '', $smile_id);
					$data['smiles'][$pack_id][] = $pack_id.'/'.$smile_id;
				}
			}
		}

		if ($_SESSION['userdata']['id'] != -1) {
			// Update self online
			DB::update('users', [
				'last_online' => time()
			], $user_selector);

			// Get ignored list
			$data['ignored'] = implode(',', $_SESSION['userdata']['ignored']);
		} else {
			DB::update('guests', [
				'last_online' => time()
			], [
				'nick = :0:',
				'bind' => [$_SESSION['userdata']['nick']]
			]);
		}

		// Get online users (last request 4 seconds ago)
		$data['online'] = DB::find('users', [
			'last_online > :0:',
			'bind' => [time() - 4]
		]);
		foreach($data['online'] as $ukey => $user) {
			$data['online'][$ukey] = [
				'id'          => $user['id'],
				'nick'        => $user['nick'],
				'gender'      => $user['gender'],
				'verificated' => $user['verificated'],
				'status'      => $user['status']
			];
		}

		// Get online guests
		$guests_online = DB::find('guests', [
			'last_online > :0:',
			'bind' => [time() - 4]
		]);
		foreach($guests_online as $gkey => $guest) $guests_online[$gkey] = $guest['nick'];
		$data['online'][] = [
			'id' => -1,
			'list' => $guests_online
		];

		$data['msgs'] = array_reverse($data['msgs']);
		exit(json_encode($data));
	}

	public static function send_msg() {
		if(isset($_SESSION['userdata'])) {
			$matches = [];
			if (preg_match('/^(\w+),/', $_POST['message'], $matches)) {
				if (in_array($matches[1], $_SESSION['userdata']['ignored'])) exit('ignored');
				$to_user = DB::find_first('users', [
					'nick = :0:',
					'bind' => [$matches[1]]
				]);

				if ($to_user && in_array($_SESSION['userdata']['nick'], explode(',', $to_user['ignored']))) {
					exit('ignored_to');
				}
			}

			$save = [
				'user_id' => $_SESSION['userdata']['id'],
				'nick' => $_SESSION['userdata']['nick'],
				'timestamp' => time(),
				'message' => $_POST['message'],
				'color' => $_POST['color']
			];

			$msg_lower = mb_strtolower($save['message'], 'UTF-8');
			if (!isset($_SESSION['antispam'])) $_SESSION['antispam'] = [];
			foreach ($_SESSION['antispam'] as $k => $m) {
				if ($m['t'] < $save['timestamp'] - 120) {
					unset($_SESSION['antispam'][$k]);
					continue;
				}

				if ($m['msg'] == $msg_lower) exit('spam');
			}
			
			$bl_filer = explode(PHP_EOL, file_get_contents(DATA.'/bl_filter'));
			foreach ($bl_filer as $word) {
				$word = trim($word);
				if ($word == '') continue;
				while (($lpos = strpos($msg_lower, $word)) !== false) {
					$wlen = strlen($word);
					$msg_lower = substr($msg_lower, 0, $lpos).'<censored>'.substr($msg_lower, $lpos + $wlen);
					$save['message'] = substr($save['message'], 0, $lpos).'<censored>'.substr($save['message'], $lpos + $wlen);
				}
			}

			DB::insert('chat', $save);
			$_SESSION['antispam'][] = [
				't' => $save['timestamp'],
				'msg' => $msg_lower
			]; 
		} else {
			View::error(403);
		}
	}

	public static function spy_msg($mode = null) {
		if ($mode) $_GET['m'] = $mode;
		if (!isset($_GET['m'])) exit;
		switch ($_GET['m']) {
			case 'enter':
				if ($_SESSION['userdata']['id'] == -1) {
					DB::insert('guests', [
						'nick' => $_SESSION['userdata']['nick'],
						'last_online' => time(),
						'kick' => ''
					]);
				}
				break;
			case 'leave':
				if ($_SESSION['userdata']['id'] == -1) {
					DB::delete('guests', [
						'nick = :0:',
						'bind' => [$_SESSION['userdata']['nick']]
					]);
				}
				break;
			case 'st':
				if ($_SESSION['userdata']['status'] == $_GET['v']) exit;
				$is_timeout = DB::find_first('chat', [
					'(message REGEXP :1:) AND timestamp >= :0:',
					'bind' => [time() - 60, 'status;'.$_SESSION['userdata']['nick']]
				]);
				if ($is_timeout != []) exit('timeout');
				$_SESSION['userdata']['status'] = $_GET['v'];
				DB::update('users', [
					'status' => $_GET['v']
				], [
					'id = :0:',
					'bind' => [$_SESSION['userdata']['id']]
				]);

				DB::insert('chat', [
					'user_id' => 0,
					'timestamp' => time(),
					'message' => 'status;'.$_SESSION['userdata']['nick'].';'.$_GET['v']
				]);
				exit;
			default:
				exit;
		}

		DB::insert('chat', [
			'user_id' => 0,
			'timestamp' => time(),
			'message' => $_GET['m'].';'.$_SESSION['userdata']['nick'].';'.$_SESSION['userdata']['country']
		]);
	}

	public static function ignore() {
		$toIgnore = DB::find_first('users', [
			'id = :0:',
			'bind' => [$_GET['u']]
		]);

		$i = array_search($toIgnore['nick'], $_SESSION['userdata']['ignored']);
		if ($i === false) {
			$_SESSION['userdata']['ignored'][] = $toIgnore['nick'];
		} else {
			unset($_SESSION['userdata']['ignored'][$i]);
		}

		DB::update('users', [
			'ignored' => implode(',', $_SESSION['userdata']['ignored'])
		], [
			'id = :0:',
			'bind' => [$_SESSION['userdata']['id']]
		]);
	}
}
