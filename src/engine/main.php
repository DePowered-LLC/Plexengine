<?php
/*
@copy
 */
// Global values
namespace pe\engine;
error_reporting(E_ALL);
date_default_timezone_set('UTC');

define('ROOT', dirname(__DIR__));
define('UPLOADS', ROOT.'/uploads');
define('DATA', ROOT.'/data');
$_CONFIG = parse_ini_file(DATA.'/config.ini');
$_CONFIG['lang_delimiters'] = explode('...', $_CONFIG['lang_delimiters']);
define('ENGINE', ROOT.'/engine');
define('TEMPLATES', ROOT.'/templates');
define('CACHE', DATA.'/cache');
define('LANG', ROOT.'/languages');
define('MODULES', DATA.'/modules');
define('TEMPLATE', TEMPLATES.'/'.$_CONFIG['template_name']);

// TODO: merge with `View` or autoloader/utils
function get_debug($errno = 0, $errstr = '') {
    require_once ENGINE.'/View.php';
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
    View::error(500, $error_text);
}
register_shutdown_function('pe\\engine\\get_debug');
set_error_handler('pe\\engine\\get_debug');

require_once 'autoloader.php';

// Parsing request URL
$url = $_GET['__url'];
if(trim($url) == '') { $url = 'index'; }
$url_parts = explode('/', $url);
unset($_GET['__url']);

// ob_start();
session_start();
if(!isset($_COOKIE['lng'])) { setcookie('lng', $_COOKIE['lng'] = $_CONFIG['default_lang']); }

// Activating modules
$_MODULES = [];
foreach (explode(',', $_CONFIG['enabled_modules']) as $module_name) {
    global $module_name;
    Router::context('pe\\modules\\'.$module_name, function () {
        global $module_name, $_MODULES;
        $module_class = 'pe\\modules\\'.$module_name.'\\Main';
        $module = new $module_class();
        $_MODULES[$module_name] = $module;
    });
}

// Connecting to DB
$dbErr = DB::connect($_CONFIG['mysql_host'], $_CONFIG['mysql_user'], $_CONFIG['mysql_pass'], $_CONFIG['mysql_db']);
if($dbErr) View::error(500, $dbErr->getMessage());

// Dispatching request
Router::dispatch($url);
if($_CONFIG['cache']) {
    exit(preg_replace('/>([ ]+)</', '><', str_replace(array("\r", "\n"), '', ob_get_clean())));
}
