<?php
load_db();
load_view();

class Auth {
    public static function is_access($required) {
        $priv = [
            'admin',
            'premium',
            'user'
        ];
        return array_search($_SESSION['userdata']['access'], $priv) <= array_search($required, $priv);
    }

    public static function sys_router() {
        global $url_parts;
        if(strpos($url_parts[0], 'id') !== false) {
            $id = substr($url_parts[0], 2);
            if ($id != -1) {
                $userdata = DB::find_first('users', [
                    'id = :0:',
                    'bind' => [$id]
                ]);
                if ($userdata) {
                    $userdata['date_of_birth'] = explode('_', $userdata['date_of_birth']);
                    $userdata['age'] = date('Y') - $userdata['date_of_birth'][2];
                    $userdata['date_of_birth'][0] = str_pad($userdata['date_of_birth'][0], 2, '0', STR_PAD_LEFT);
                    $userdata['date_of_birth'][1] = str_pad($userdata['date_of_birth'][1], 2, '0', STR_PAD_LEFT);
                    switch (intval($userdata['date_of_birth'][1])) {
                        case 1:
                            if($userdata['date_of_birth'][0] < 21) $zodiac = 1;
                            else $zodiac = 2;
                            break;
                        case 2:
                            if($userdata['date_of_birth'][0] < 21) $zodiac = 2;
                            else $zodiac = 3;
                            break;
                        case 3:
                            if($userdata['date_of_birth'][0] < 21) $zodiac = 3;
                            else $zodiac = 4;
                            break;
                        case 4:
                            if($userdata['date_of_birth'][0] < 21) $zodiac = 4;
                            else $zodiac = 5;
                            break;
                        case 5:
                            if($userdata['date_of_birth'][0] < 21) $zodiac = 5;
                            else $zodiac = 6;
                            break;
                        case 6:
                            if($userdata['date_of_birth'][0] < 22) $zodiac = 6;
                            else $zodiac = 7;
                            break;
                        case 7:
                            if($userdata['date_of_birth'][0] < 23) $zodiac = 7;
                            else $zodiac = 8;
                            break;
                        case 8:
                            if($userdata['date_of_birth'][0] < 24) $zodiac = 8;
                            else $zodiac = 9;
                            break;
                        case 9:
                            if($userdata['date_of_birth'][0] < 24) $zodiac = 9;
                            else $zodiac = 10;
                            break;
                        case 10:
                            if($userdata['date_of_birth'][0] < 24) $zodiac = 10;
                            else $zodiac = 11;
                            break;
                       case 11:
                            if($userdata['date_of_birth'][0] < 23) $zodiac = 11;
                            else $zodiac = 12;
                            break;
                        case 12:
                            if($userdata['date_of_birth'][0] < 22) $zodiac = 12;
                            else $zodiac = 1;
                            break;
                        default:
                            $zodiac = 1;
                            break;
                    }
                    $userdata['zodiac'] = View::lang('zodiac'.$zodiac);
                    $userdata['date_of_birth'] = implode('.', $userdata['date_of_birth']);
                    $userdata['about'] = json_decode($userdata['about'], true);
                    $GLOBALS['profile_data'] = $userdata;
                }

                if (isset($_SESSION['userdata']) && $_SESSION['userdata']['id'] != -1) {
                    $is_nsended = DB::find_first('notifications', [
                        'user_id = :0: AND sender_id = :1: AND timestamp > :2:',
                        'bind' => [$GLOBALS['profile_data']['id'], $_SESSION['userdata']['id'], time() - 3600]
                    ]);
                    
                    if (!$is_nsended) {
                        DB::insert('notifications', [
                            'user_id' => $GLOBALS['profile_data']['id'],
                            'sender_id' => $_SESSION['userdata']['id'],
                            'info' => json_encode(['sender_nick' => $_SESSION['userdata']['nick']]),
                            'type' => 'profile_view',
                            'timestamp' => time()
                        ]);
                    }
                }
            }

            View::load('profile');
            exit;
        } elseif ($url_parts[0] == 'notifications') {
            $GLOBALS['list'] = DB::find('notifications', [
                'user_id = :0: LIMIT 0,30',
                'bind' => [$_SESSION['userdata']['id']]
            ]);
            $GLOBALS['list'] = array_reverse($GLOBALS['list']);
            View::load('notifications');
            exit;
        }
    }

    public static function profile_save() {
        $fields = ['info', 'city', 'family_status', 'work', 'site', 'vk', 'inst', 'phone', 'skype'];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                if ($_POST[$field] == 'none') {
                    if (isset($_SESSION['userdata']['about'][$field])) unset($_SESSION['userdata']['about'][$field]);
                } else {
                    $_SESSION['userdata']['about'][$field] = substr(htmlspecialchars($_POST[$field]), 0, 26);
                }
            }
        }

        DB::update('users', [
            'about' => json_encode($_SESSION['userdata']['about'])
        ], [
            'id = :0:',
            'bind' => [$_SESSION['userdata']['id']]
        ]);
    }
    
    public static function exchange() {
        global $_CONFIG;
        if ($_SESSION['userdata']['id'] == -1 && !isset($_GET['v'])) exit;
        $val = intval($_GET['v']);
        if ($_SESSION['userdata']['points'] >= $val) {
            $_SESSION['userdata']['points'] -= $val;
            $_SESSION['userdata']['credits'] += $val / $_CONFIG['exchange_rate'];
            DB::update('users', [
                'points' => $_SESSION['userdata']['points'],
                'credits' => $_SESSION['userdata']['credits']
            ], [
                'id = :0:',
                'bind' => [$_SESSION['userdata']['id']]
            ]);
            exit('success');
        } else exit('no_points');
    }

    public static function router() {
        global $url_parts;
        if($url_parts[0] == 'captcha.png') self::captcha();
        else self::{$url_parts[0]}();
    }
    
    public static function register() {
        // $check = Utils::check([
        //     'email' => [
        //         'type' => 'string',
        //         'validate' => FILTER_VALIDATE_EMAIL
        //     ]
        // ]);
        if(
            (!isset($_POST['email']) || trim($_POST['email']) == '')
         || (!isset($_POST['nick']) || trim($_POST['nick']) == '')
         || (!isset($_POST['pass']) || trim($_POST['pass']) == '')
         || (!isset($_POST['birth_day']) || trim($_POST['birth_day']) == '')
         || (!isset($_POST['birth_month']) || trim($_POST['birth_month']) == '')
         || (!isset($_POST['birth_year']) || trim($_POST['birth_year']) == '')
         || (!isset($_POST['gender']) || trim($_POST['gender']) == '')
         || (!isset($_POST['country']) || trim($_POST['country']) == '')
        ) { exit('fill_fields'); }
        else {
            $user_email = [
                'email = :0:',
                'bind' => [$_POST['email']]
            ];
            
            $user_nick = [
                'nick = :0:',
                'bind' => [$_POST['nick']]
            ];
            
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) exit('invalid_email');
            elseif(DB::find_first('users', $user_email)) exit('user_email_exist');
            elseif(DB::find_first('users', $user_nick)) exit('user_nick_exist');
            elseif(mb_strlen($_POST['nick']) > 16 || mb_strlen($_POST['nick']) < 3) exit('long_nick');
            elseif($_POST['pass'] != $_POST['passr']) exit('passwords_doesnt_match');
            else {
                $userdata = [
                    'id' => null,
                    'email' => $_POST['email'],
                    'nick' => $_POST['nick'],
                    'country' => $_POST['country'],
                    'gender' => $_POST['gender'],
                    'date_of_birth' => implode('_', [$_POST['birth_day'], $_POST['birth_month'], $_POST['birth_year']]),
                    'pass' => md5(sha1($_POST['pass'])),
                    'access' => 'user',
                    'ignored' => '',
                    'status' => 'chat',
                    'about' => '{}',
                    'credits' => 0,
                    'points' => 0
                ];
                $userdata['id'] = DB::insert('users', $userdata);
                $userdata['ignored'] = [];
                $_SESSION['userdata'] = $userdata;
                exit('success');
            }
        }
    }
    
    public static function login() {
        $user = DB::find_first('users', [
            'email = :0: OR nick = :0:',
            'bind' => [$_POST['login']]
        ]);
        if($user) {
            if($user['pass'] != md5(sha1($_POST['pass']))) {
                exit('wrong_password');
            } else {
                $user['ignored'] = explode(',', $user['ignored']);
                if ($user['ignored'][0] == '') unset($user['ignored'][0]);
                $user['about'] = json_decode($user['about'], true);
                $_SESSION['userdata'] = $user;
                exit('success');
            }
        } else {
            exit('user_not_found');
        }
    }
    
    public static function guest() {
        $check = Utils::check([
            'nick' => [             // $_POST['nick']
                'type' => 'string', // Field type
                'min' => 3,         // Minimal string length
                'max' => 16,        // Maximal string length
                'utf' => true       // Allow UTF-8
            ],
            'country' => [
                'type' => 'string'
            ],
            'captcha' => [
                'type' => 'string',
                'value' => $_SESSION['captcha'], // Value for eqeq
                'case_sens' => false // Is string case sensitive
            ]
        ]);

        if ($check == 'success') {
            if (DB::find_first('users', [
                'nick = :0:',
                'bind' => [$_POST['nick']]
            ]) || DB::find_first('guests', [
                'nick = :0: AND last_online >= :1:',
                'bind' => [$_POST['nick'], time() - 5]
            ])) exit('nick_used');
            
            DB::insert('guests', [
                'nick' => $_POST['nick'],
                'last_online' => time(),
                'limitation' => ''
            ]);

            $_SESSION['userdata'] = [
                'id' => -1,
                'nick' => $_POST['nick'],
                'country' => $_POST['country'],
                'credits' => 0,
                'gender' => 'guest',
                'last_online' => time(),
                'ignored' => [],
                'access' => 'user',
                'status' => 'chat'
            ];
        }
        
        exit($check);
    }
    
    public static function logout() {
        session_destroy();
        header('Location: /');
    }

    public static function captcha() {
        require_once 'captcha/index.php';
        
        $fonts = scandir(__DIR__.'/captcha/fonts');
        array_shift($fonts);
        array_shift($fonts);
        foreach($fonts as $key => $val) $fonts[$key] = __DIR__.'/captcha/fonts/'.$val;
        
        $cData = simple_php_captcha([
            'min_font_size' => 20,
            'max_font_size' => 32,
            'angle_min' => -30,
            'angle_max' => 30,
            'fonts' => $fonts,
            'characters' => 'ABCDEFGHJKLMNPRSTUVWXYZ0123456789',
        ]);
        
        $_GET = parse_str(explode('?', $cData['image_src'])[1]);
        captcha_image();
        $_SESSION['captcha'] = $cData['code'];
    }

    public static function upload_photo() {
        if (!isset($_SESSION['userdata'])) exit('forbidden');
        else if ($_SESSION['userdata']['id'] == -1) exit('guest');
        else if (!isset($_FILES['file'])) exit('no_file');
        else {
            $img = imagecreatefromstring(file_get_contents($_FILES['file']['tmp_name']));
            if ($img === false) {
                imagedestroy($img);
                exit('wrong_format');
            }

            $task = imagerotate($img, 360 - $_POST['rotate'], 0);
            if ($task === false) {
                imagedestroy($img);
                exit('rotate_error');
            } else $img = $task;

            $task = imagecrop($img, $_POST);
            if ($task === false) {
                imagedestroy($img);
                exit('crop_error');
            } else $img = $task;
            
            $task = imagejpeg($img, DATA.'/avatars/id'.$_SESSION['userdata']['id'].'.jpg');
            imagedestroy($img);
            if ($task === false) exit('save_error');
            else exit('success');
        }
    }
}