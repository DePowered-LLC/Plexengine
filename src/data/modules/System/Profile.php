<?php
namespace pe\modules\System;
use pe\engine\View;
use pe\engine\DB;

class Profile {
    public static function view ($params) {
        $userdata = null;
        if ($params['uid'] != -1) {
            $userdata = DB::find_first('users', [
                'id = :0:',
                'bind' => [$params['uid']]
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

                if (isset($_SESSION['userdata']) && $_SESSION['userdata']['id'] != -1) {
                    Notifications::_add([
                        'type' => 'profile_view',
                        'user_id' => $userdata['id'],
                        'info' => [
                            'sender_nick' => $_SESSION['userdata']['nick']
                        ]
                    ]);
                }
            }
        }

        View::load('profile', [
            'profile' => $userdata
        ]);
        exit;
    }

    public static function save () {
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
            
            $task = imagejpeg($img, UPLOADS.'/avatars/id'.$_SESSION['userdata']['id'].'.jpg');
            imagedestroy($img);
            if ($task === false) exit('save_error');
            else exit('success');
        }
    }
}
