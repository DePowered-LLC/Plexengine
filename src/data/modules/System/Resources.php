<?php
/*
@copy
 */

namespace pe\modules\System;
use pe\engine\DB;
use pe\engine\View;

class Resources {
    public static function load ($path) {
        if(file_exists($path) && is_file($path)) {
            $ext = explode('.', $path);
            $ext = $ext[count($ext) - 1];
            switch($ext) {
                case 'css': $mime = 'text/css';        break;
                case 'js':  $mime = 'text/javascript'; break;
                case 'gif': $mime = 'image/gif';       break;
                case 'png': $mime = 'image/png';       break;
                case 'jpg': $mime = 'image/jpg';       break;
                default: $mime = mime_content_type($path); break;
            }

            $file_hash = filemtime($path);
            header('ETag: '.$file_hash);
            if (isset($_SERVER['If-None-Match']) && $_SERVER['If-None-Match'] == $file_hash) {
                http_response_code(304);
                exit;
            }

            header('Content-Type: '.$mime);
            $seconds_to_cache = 3600;
            $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
            header("Expires: $ts");
            header("Pragma: cache");
            header("Cache-Control: max-age=$seconds_to_cache");
            readfile($path);
            exit;
        } else View::error(404, 'File `'.$path.'` not found');
    }

    public static function getPublic ($params) {
        $path = TEMPLATE.'/'.$params['file'];

        $ext_deny = ['tpl'];
        $file_ext = explode('.', $path);
        $file_ext = $file_ext[count($file_ext)-1];

        if(!in_array($file_ext, $ext_deny)) self::load($path);
        else View::error(403, 'Was requested forbidden file type');
    }
    
    public static function getDirectUpload ($params) { self::load(UPLOADS.'/'.$params['file']); }
    public static function getUpload ($params) {
        $path = UPLOADS.'/'.$params['type'].'/'.$params['file'];
        switch ($params['type']) {
            case 'covers':
                if ($params['file'] == 'id-1.jpg' || !file_exists($path)) {
                    if (isset($_GET['s'])) $path = TEMPLATE.'/img/no_cover.png';
                    else $path = TEMPLATE.'/img/no_cover_profile.png';
                }
                break;
            case 'avatars':
                if ($params['file'] == 'id-1.jpg') $path = TEMPLATE.'/img/guest_avatar_full.png';
                else if (!file_exists($path)) {
                    $uid = preg_replace('/^id(-?[0-9]+).jpg$/', '$1', $params['file']);
                    $u = DB::find_first('users', [
                        'id = :0:',
                        'bind' => [$uid]
                    ]);
                    if ($u) $path = TEMPLATE.'/img/'.$u['gender'].'_avatar_full.png';
                }
                break;
        }
        self::load($path);
    }

    public static function loadView ($params) { View::load($params['path']); }
}