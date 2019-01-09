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
                    $userdata['date_of_birth'][0] = str_pad($userdata['date_of_birth'][0], 2, '0', STR_PAD_LEFT);
                    $userdata['date_of_birth'][1] = str_pad($userdata['date_of_birth'][1], 2, '0', STR_PAD_LEFT);
                    switch (intval($userdata['date_of_birth'][1])) {
                        case 1:
                            if($userdata['date_of_birth'][0] < 21) $userdata['zodiac'] = 'kozerog';
                            else $userdata['zodiac'] = 'vodolei';
                            break;
                        case 2:
                            if($userdata['date_of_birth'][0] < 21) $userdata['zodiac'] = 'vodolei';
                            else $userdata['zodiac'] = 'ribi';
                            break;
                        case 3:
                            if($userdata['date_of_birth'][0] < 21) $userdata['zodiac'] = 'ribi';
                            else $userdata['zodiac'] = 'oven';
                            break;
                        case 4:
                            if($userdata['date_of_birth'][0] < 21) $userdata['zodiac'] = 'oven';
                            else $userdata['zodiac'] = 'telec';
                            break;
                        case 5:
                            if($userdata['date_of_birth'][0] < 21) $userdata['zodiac'] = 'telec';
                            else $userdata['zodiac'] = 'blizneci';
                            break;
                        case 6:
                            if($userdata['date_of_birth'][0] < 22) $userdata['zodiac'] = 'blizneci';
                            else $userdata['zodiac'] = 'rak';
                            break;
                        case 7:
                            if($userdata['date_of_birth'][0] < 23) $userdata['zodiac'] = 'rak';
                            else $userdata['zodiac'] = 'lev';
                            break;
                        case 8:
                            if($userdata['date_of_birth'][0] < 24) $userdata['zodiac'] = 'lev';
                            else $userdata['zodiac'] = 'deva';
                            break;
                        case 9:
                            if($userdata['date_of_birth'][0] < 24) $userdata['zodiac'] = 'deva';
                            else $userdata['zodiac'] = 'vesi';
                            break;
                        case 10:
                            if($userdata['date_of_birth'][0] < 24) $userdata['zodiac'] = 'vesi';
                            else $userdata['zodiac'] = 'scorpion';
                            break;
                       case 11:
                            if($userdata['date_of_birth'][0] < 23) $userdata['zodiac'] = 'scorpion';
                            else $userdata['zodiac'] = 'strelec';
                            break;
                        case 12:
                            if($userdata['date_of_birth'][0] < 22) $userdata['zodiac'] = 'strelec';
                            else $userdata['zodiac'] = 'kozerog';
                            break;
                        default:
                            $userdata['zodiac'] = 'kozerog';
                            break;
                    }
                    $userdata['date_of_birth'] = implode('.', $userdata['date_of_birth']);
                    $userdata['about'] = json_decode($userdata['about'], true);
                    $GLOBALS['profile_data'] = $userdata;
                }
            }
            
            View::load('profile');
            exit;
        }
    }

    public static function profile_save() {
        $fields = ['info', 'city', 'family_status', 'work', 'site', 'vk', 'inst', 'phone', 'skype'];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) $_SESSION['userdata']['about'][$field] = substr(htmlspecialchars($_POST[$field]), 0, 26);
            else if (isset($_SESSION['userdata']['about'][$field])) unset($_SESSION['userdata']['about'][$field]);
        }

        DB::update('users', [
            'about' => json_encode($_SESSION['userdata']['about'])
        ], [
            'id = :0:',
            'bind' => [$_SESSION['userdata']['id']]
        ]);
    }
    
    public static function router() {
        global $url_parts;
        if($url_parts[0] == 'captcha.png') self::captcha();
        else self::{$url_parts[0]}();
    }
    
    public static function register() {
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
            elseif(strlen($_POST['nick']) > 16) exit('long_nick');
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
                    'credits' => 0
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
        if(!isset($_POST['nick']) || trim($_POST['nick']) == '') exit('enter_nick');
        if(strlen($_POST['nick']) > 16) exit('long_nick');
        if(!isset($_POST['country']) || trim($_POST['country']) == '') exit('no_country');
        if(strtoupper($_POST['captcha']) != $_SESSION['captcha']) exit('wrong_captcha');
        
        $_SESSION['userdata'] = [
            'id' => -1,
            'nick' => $_POST['nick'],
            'country' => $_POST['country'],
            'credits' => 0,
            'gender' => 'guest',
            'ignored' => [],
            'access' => 'user',
            'status' => 'chat'
        ];
        exit('success');
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
}