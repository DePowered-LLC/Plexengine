<?php
/*
@copy
 */
error_reporting(E_ALL);
date_default_timezone_set('UTC');

define('ROOT', dirname(__DIR__));
define('ENGINE', ROOT.'/engine');
define('TEMPLATES', ROOT.'/templates');
define('DATA', ROOT.'/data');
define('CACHE', DATA.'/cache');
define('LANG', ROOT.'/languages');
define('MODULES', DATA.'/modules');

$_CONFIG = parse_ini_file(DATA.'/config.ini');
$_CONFIG['lang_delimiters'] = explode('...', $_CONFIG['lang_delimiters']);
$_CONFIG['loaded_modules'] = explode(',', $_CONFIG['loaded_modules']);
foreach($_CONFIG['loaded_modules'] as $key => $val) {
    $_CONFIG['loaded_modules'][$key] = trim($val);
}
define('TEMPLATE', TEMPLATES.'/'.$_CONFIG['template_name']);

$url = $_GET['__url'];
if(trim($url) == '') { $url = 'index'; }
$url_parts = explode('/', $url);
unset($_GET['__url']);

// ob_start();
function get_debug($errno = 0, $errstr = '') {
    // ob_end_clean();
    $error_codes = [
        'E_USER_DEPRECATED' => '[Script] Deprecated construction',
        'E_DEPRECATED' => 'Deprecated construction',
        'E_ERROR' => 'Fatal error',
        'E_RECOVERABLE_ERROR' => 'Fatal error but PHP not crushed',
        'E_USER_ERROR' => '[Script] Fatal error',
        'E_WARNING' => 'Warning',
        'E_USER_WARNING' => '[Script] Warning',
        'E_PARSE' => 'Parser error',
        'E_NOTICE' => 'Notice',
        'E_USER_NOTICE' => '[Script] Notice'
    ];
    $e = error_get_last();
    if(!$e) { return false; }
    $error_type = '';
    foreach($error_codes as $const => $name) {
        if(constant($const) == $e['type']) { $error_type = $name; break; }
    }
    
    if(strpos('with message \'', $e['message']) !== false) {
        $error_message = explode('with message \'', $e['message'])[1];
        $error_message = explode('\' in /', $error_message)[0];
    } else {
        $error_message = $e['message'];
    }
    
    if(strpos('Stack trace', $e['message']) !== false) {
        $trace = 'Stack trace:'.str_replace('thrown', '', explode('Stack trace:', $e['message'])[1]);
    } else {
        $trace = 'Original message:'.PHP_EOL.$e['message'];
    }
    
    $error_text = htmlspecialchars($error_type.PHP_EOL.'File: '.$e['file'].PHP_EOL.'Line: '.$e['line'].PHP_EOL.PHP_EOL.$error_message.PHP_EOL.PHP_EOL.$trace);
    
    require_once ENGINE.'/view.php';
    View::error(500, $error_text);
}
register_shutdown_function('get_debug');
set_error_handler('get_debug');

session_start();
if(!isset($_COOKIE['lng'])) { setcookie('lng', $_COOKIE['lng'] = $_CONFIG['default_lang']); }
// try {

foreach($_CONFIG['loaded_modules'] as $module_name) {
    if(file_exists(MODULES.'/'.$module_name.'/main.php')) {
        include_once MODULES.'/'.$module_name.'/main.php';
        if(class_exists($module_name)) {
            if(method_exists($module_name, 'sys_router')) {
                call_user_func_array([$module_name, 'sys_router'], []);
            }
        } else {
            throw new Exception('Module "'.$module_name.'" not found');
        }
    } else {
        throw new Exception('Main file of module "'.$module_name.'" not found');
    }
}

if($url_parts[0] == 'public') {
    array_shift($url_parts);
    switch ($url_parts[0]) {
        case 'smiles':
            array_shift($url_parts);
            $file_path = DATA.'/smiles/'.implode('/', $url_parts);
            break;
        case 'covers':
            $file_path = DATA.'/covers/'.$url_parts[1];
            if (!file_exists($file_path)) $file_path = TEMPLATE.'/img/no_cover'.(isset($_GET['s'])?'':'_profile').'.png';
            break;
        case 'avatars':
            $file_path = DATA.'/avatars/'.$url_parts[1];
            
            if (!file_exists($file_path)) {
                $uid = substr(explode('.png', $url_parts[1])[0], 2);
                if ($uid == '-1') $file_path = TEMPLATE.'/img/guest_avatar_full.png';
                $u = DB::find_first('users', [
                    'id = :0:',
                    'bind' => [$uid]
                ]);
                if ($u) $file_path = TEMPLATE.'/img/'.$u['gender'].'_avatar_full.png';
            }
            break;
        default:
            $file_path = TEMPLATE.'/'.implode('/', $url_parts);
            break;
    }
    
    $ext_deny = ['tpl'];
    $file_ext = explode('.', $file_path);
    $file_ext = $file_ext[count($file_ext)-1];

    if(!in_array($file_ext, $ext_deny) && file_exists($file_path) && is_file($file_path)) {
        switch(explode('.', $url_parts[count($url_parts)-1])[1]) {
            case 'css': $mime = 'text/css';        break;
            case 'js':  $mime = 'text/javascript'; break;
            default: $mime = mime_content_type($file_path); break;
        }

        $file_hash = hash_file('md5', $file_path);
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
        readfile($file_path);
        exit;
    } else {
        require_once 'view.php';
        View::error(404);
    }
} elseif($url_parts[0] == 'modules') {
    $module_name = $url_parts[1];
    if(file_exists(MODULES.'/'.$module_name.'/main.php')) {
        require_once MODULES.'/'.$module_name.'/main.php';
        if(class_exists($module_name)) {
            if(method_exists($module_name, 'router')) {
                array_shift($url_parts);
                array_shift($url_parts);
                call_user_func_array([$module_name, 'router'], []);
                exit;
            } else {
                throw new Exception('Router of module "'.$module_name.'" not found');
            }
        } else {
            throw new Exception('Module "'.$module_name.'" not found');
        }
    } else {
        throw new Exception('Main file of module "'.$module_name.'" not found');
    }
}

require_once 'noienedge.php';
require_once 'db.php';
require_once 'view.php';

function load_db() {
    require_once ENGINE.'/db.php';
    global $_CONFIG;
    $e = DB::connect($_CONFIG['mysql_host'], $_CONFIG['mysql_user'], $_CONFIG['mysql_pass'], $_CONFIG['mysql_db']);
    if($e) {
        require_once ENGINE.'/view.php';
        View::error(500, $e->getMessage());
    }
}

function load_view() {
    require_once ENGINE.'/view.php';
}

DB::connect($_CONFIG['mysql_host'], $_CONFIG['mysql_user'], $_CONFIG['mysql_pass'], $_CONFIG['mysql_db']);
View::load($url);

if($_CONFIG['cache']) {
    exit(preg_replace('/>([ ]+)</', '><', str_replace(array("\r", "\n"), '', ob_get_clean())));
}
// } catch(Exception $e) {
//     get_debug();
// }