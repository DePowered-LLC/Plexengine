<?php
namespace pe\modules\System;
use pe\engine\Utils;
use pe\engine\DB;

class Auth {
    // Captcha
    public static function captcha () {
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

    // Authentication
    public static function is_access ($required) {
        $priv = [
            'admin',
            'premium',
            'user',
            'none'
        ];
        return array_search(isset($_SESSION['userdata']) ? $_SESSION['userdata']['access'] : 'none', $priv) <= array_search($required, $priv);
    }
    
    public static function login () {
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

    public static function logout () {
        session_destroy();
        header('Location: /');
    }

    // Registration
    public static function register () {
        // TODO: Use fields global validator
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

    public static function guest () {
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
}
