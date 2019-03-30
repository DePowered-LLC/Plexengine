<?php
namespace pe\modules\System;
use pe\engine\Utils;
use pe\engine\View;
use pe\engine\DB;

class Helper extends StaticEventEmitter {
	public static function load_data ($params) {
		$data = [];
		if (!isset($_SESSION['userdata'])) {
			View::error(403, 'No read access');
			exit;
		}

		$user_table = $_SESSION['userdata']['id'] != -1 ? 'users' : 'guests';
		$user_selector = $_SESSION['userdata']['id'] != -1 ? [
			'id = :0:',
			'bind' => [$_SESSION['userdata']['id']]
		] : [
			'nick = :0:',
			'bind' => [$_SESSION['userdata']['nick']]
		];

		// Check user limitations
		$limitation = DB::find_first($user_table, $user_selector);
		$limitation = $limitation['limitation'];
		$limitation = explode(';', $limitation);
		switch ($limitation[0]) {
			case 'kick':
				session_destroy();
				DB::update($user_table, [
					'limitation' => ''
				], $user_selector);
				break;
			case 'ban':
				if ($limitation[2] <= time()) {
					DB::update($user_table, [
						'limitation' => ''
					], $user_selector);
					break;
				} else {
					session_destroy();
					exit(json_encode([
						'limitation' => $limitation
					]));
				}
			case 'mute':
				if ($limitation[2] <= time()) {
					DB::update($user_table, [
						'limitation' => ''
					], $user_selector);
				}
				break;
		}
		$data['limitation'] = $limitation;

		if(isset($_GET['t']) && $_GET['t'] > 0) {
			if ($_GET['t'] > 15) $_GET['t'] = 15;
			$result = self::emit('load_messages', $_GET['t']);
			if (is_array($result)) $data['msgs'] = $result;
			else $data['msgs'] = DB::find('chat', [
				'timestamp >= :0: ORDER BY id DESC',
				'bind' => [time() - intval($_GET['t']) - 1]
			]);
		} else {
			global $_CONFIG;
			self::spy_msg('enter');

			$result = self::emit('load_messages', -1);
			if (is_array($result)) $data['msgs'] = $result;
			else $data['msgs'] = DB::find('chat', 'ORDER BY id DESC LIMIT 0,'.$_CONFIG['messages_limit']);
			
			// Get smile packs
			$data['smiles'] = [];
			$smile_packs = scandir(UPLOADS.'/smiles');
			array_shift($smile_packs);
			array_shift($smile_packs);
			foreach($smile_packs as $pack_id) {
				$data['smiles'][$pack_id] = [];
				$smile_list = scandir(UPLOADS.'/smiles/'.$pack_id);
				array_shift($smile_list);
				array_shift($smile_list);
				foreach($smile_list as $smile_id) {
					if($smile_id == 'icon.png') continue;
					$smile_id = str_replace('.png', '', $smile_id);
					$data['smiles'][$pack_id][] = $pack_id.'/'.$smile_id;
				}
			}
		}

		$data['msgs'] = array_reverse($data['msgs']);

		// Update self online
		DB::update($user_table, [
			'last_online' => time()
		], $user_selector);
		
		// Get ignored list
		if ($_SESSION['userdata']['id'] != -1) {
			$data['ignored'] = implode(',', $_SESSION['userdata']['ignored']);
		}

		// Get online users (last request 4 seconds ago)
		$online_info = self::emit('get_online');
		if ($online_info) {
			$data['online'] = $online_info['users'];
		} else {
			$data['online'] = DB::find('users', [
				'last_online > :0:',
				'bind' => [time() - 4]
			]);
		}

		foreach($data['online'] as $ukey => $user) {
			$data['online'][$ukey] = [
				'id'          => $user['id'],
				'nick'        => $user['nick'],
				'gender'      => $user['gender'],
				'access'      => $user['access'],
				'verificated' => $user['verificated'],
				'status'      => strpos($user['limitation'], 'mute') === 0 ? 'muted' : $user['status']
			];
		}

		// Get online guests
		if ($online_info) {
			$guests_online = $online_info['guests'];
		} else {
			$guests_online = DB::find('guests', [
				'last_online > :0:',
				'bind' => [time() - 4]
			]);
		}

		foreach($guests_online as $gkey => $guest) $guests_online[$gkey] = $guest['nick'];
		$data['online'][] = [
			'id' => -1,
			'list' => $guests_online
		];

		// Get notifications
		$data['notifications'] = [];
		if ($_SESSION['userdata']['id'] != -1) {
			$data['notifications'] = DB::find('notifications', [
				'user_id = :0: AND is_readed = false',
				'bind' => [$_SESSION['userdata']['id']]
			]);
		}

		exit(json_encode($data));
	}

	public static function load_msg () {
		$msg = self::emit('get_message', $_GET['id']);
		if (!$msg) $msg = DB::find_first('chat', [
			'id = :0:',
			'bind' => [$_GET['id']]
		]);

		if (!$msg) exit('no_message');
		else exit(json_encode($msg));
	}

	public static function send_msg () {
		if(isset($_SESSION['userdata'])) {
			$user_table = $_SESSION['userdata']['id'] != -1 ? 'users' : 'guests';
			$user_selector = $_SESSION['userdata']['id'] != -1 ? [
				'id = :0:',
				'bind' => [$_SESSION['userdata']['id']]
			] : [
				'nick = :0:',
				'bind' => [$_SESSION['userdata']['nick']]
			];

			$limitation = DB::find_first($user_table, $user_selector)['limitation'];
			if (strpos($limitation, 'mute') === 0) exit($limitation);

			$matches = [];
			global $_CONFIG;
			$bot_answer = false;
			if (preg_match('/^(['.$_CONFIG['nick_regexp'].']+) >> (.*)/', $_POST['message'], $matches)) {
				if (trim($matches[2]) == '') exit;
				if (in_array($matches[1], $_SESSION['userdata']['ignored'])) exit('ignored');
				if ($matches[1] == View::lang('spy_nick')) $bot_answer = true;
				else {
					$to_user = DB::find_first('users', [
						'nick = :0:',
						'bind' => [$matches[1]]
					]);
	
					if ($to_user && in_array($_SESSION['userdata']['nick'], explode(',', $to_user['ignored']))) {
						exit('ignored_to');
					}
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
			$antispam_counter = 0;
			foreach ($_SESSION['antispam'] as $k => $m) {
				if ($m['t'] < $save['timestamp'] - 120) {
					unset($_SESSION['antispam'][$k]);
					continue;
				}
				if ($m['msg'] == $msg_lower) exit('spam');
				if ($m['t'] > time() - 5) $antispam_counter++;
				if ($antispam_counter > 2) {
					DB::update($user_table, [
						'limitation' => 'kick;'.View::lang('antispam')
					], [
						'nick = :0:',
						'bind' => [$_SESSION['userdata']['nick']]
					]);

					DB::insert('chat', [
						'user_id' => 0,
						'timestamp' => time(),
						'message' => 'kick;'.$_SESSION['userdata']['nick'].';'.$kick_msg
					]);
					exit('spam_rate');
				}
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

			$result = self::emit('send_message', $save);
			if (!$result) DB::insert('chat', $save);
			$_SESSION['antispam'][] = [
				't' => $save['timestamp'],
				'msg' => $msg_lower
			];

			if ($_SESSION['userdata']['id'] != -1) {
				$_SESSION['userdata']['points'] += 10;
				DB::update('users', [
					'points' => $_SESSION['userdata']['points']
				], [
					'id = :0:',
					'bind' => [$_SESSION['userdata']['id']]
				]);
			}

			if ($bot_answer) {
				$answers = explode(PHP_EOL, file_get_contents(DATA.'/bot_msgs'));
				$bot_answer = [
					'user_id' => 0,
					'timestamp' => time() + 1,
					'message' => $_SESSION['userdata']['nick'].', '.$answers[rand(0, count($answers) - 1)]
				];

				$result = self::emit('send_message', $bot_answer);
				if (!$result) DB::insert('chat', $bot_answer);
			}
		} else {
			View::error(403);
		}
	}

	public static function spy_msg ($mode = null) {
		if ($mode && !isset($mode['action'])) $_GET['m'] = $mode;
		if (!isset($_GET['m'])) exit;
		
		$nick = $_SESSION['userdata']['nick'];
		$nick = str_replace(';', '&#59', $nick);
		
		switch ($_GET['m']) {
			case 'enter':
			case 'leave':
				self::emit($_GET['m']);
				break;
			case 'st':
				if ($_SESSION['userdata']['id'] == -1) exit('guest');
				if ($_SESSION['userdata']['status'] == $_GET['v']) exit;
				$st_selector = [
					'(message REGEXP :1:) AND timestamp >= :0:',
					'bind' => [time() - 60, 'status;'.$nick]
				];

				$is_timeout = self::emit('get_message', $st_selector);
				if (!$is_timeout) $is_timeout = DB::find_first('chat', $st_selector);
				if ($is_timeout) exit('timeout');

				$_SESSION['userdata']['status'] = $_GET['v'];
				DB::update('users', [
					'status' => $_GET['v']
				], [
					'id = :0:',
					'bind' => [$_SESSION['userdata']['id']]
				]);

				$msg = [
					'user_id' => 0,
					'timestamp' => time(),
					'message' => 'status;'.$nick.';'.$_GET['v']
				];

				$result = self::emit('send_message', $msg);
				if (!$result) DB::insert('chat', $msg);
				exit;
			default:
				exit;
		}

		$msg = [
			'user_id' => 0,
			'timestamp' => time(),
			'message' => $_GET['m'].';'.$nick.';'.$_SESSION['userdata']['country']
		];

		$result = self::emit('send_message', $msg);
		if (!$result) DB::insert('chat', $msg);
	}

	public static function ignore () {
		if (isset($_GET['n'])) {
			$nick = $_GET['n'];
		} else {
			$nick = DB::find_first('users', [
				'id = :0:',
				'bind' => [$_GET['u']]
			])['nick'];
		}

		$i = array_search($nick, $_SESSION['userdata']['ignored']);
		if ($i === false) {
			$_SESSION['userdata']['ignored'][] = $nick;
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
