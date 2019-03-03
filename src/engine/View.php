<?php
/*
@copy
 */
// ob_start();
namespace pe\engine;
class View {
    // TODO: Error pages template
    public static function error($err, $debug = '') {
        ob_end_clean();
        global $_CONFIG;
        $tpl_path = TEMPLATE.'/errors/'.$err.'.tpl';
        if(file_exists($tpl_path)) {
            self::load($tpl_path);
        } else {
            if (strstr($debug, "eval()'d code") !== false) {
                $matches = [];
                preg_match('/Line: ([0-9]+)/', $debug, $matches);
                $debug .= PHP_EOL.PHP_EOL.'Template:'.PHP_EOL;
                foreach (explode(PHP_EOL, self::$last_tpl_cont) as $key => $val) {
                    $d = ':';
                    if ($key + 1 == $matches[1]) $d = '&gt;';
                    $debug .= ($key + 1).$d.' '.$val;
                }
            }
            $debug = str_replace(ROOT, '', $debug);
            require_once 'errors/'.$err.'.php';
        }
        exit;
    }
    
    public static function load ($tpl, $vars = []) {
        $tpl_path = TEMPLATE.'/'.$tpl.'.tpl';
        if(!file_exists(CACHE)) { mkdir(CACHE, 0777); }
        $cache_path = CACHE.'/'.str_replace('/', '_', $tpl).'.php';
        global $_CONFIG;
        if(file_exists($cache_path) && $_CONFIG['cache']) {
            try {
                include $cache_path;
            } catch(Exception $e) {
                self::error(500, 'Error in executing template'.PHP_EOL.get_debug($e));
            }
        } else {
            if(file_exists($tpl_path)) {
                $tpl_cont = file_get_contents($tpl_path);
                $bom = pack('H*', 'EFBBBF');
                $tpl_cont = preg_replace("/^$bom/", '', $tpl_cont);
                echo self::parse($tpl_cont, $tpl, $vars);
            } else {
                self::error(404, '[Template] '.$tpl_path);
            }
        }
    }
    
    private static $last_tpl_cont = '';
    private static function parse($tpl_cont, $tpl, $_vars) {
        // Removing comments
        $tpl_cont = preg_replace('/{#(?:.|\n)*?#}/', '', $tpl_cont);
        // Parsing include statements
        $tpl_cont = preg_replace_callback('/{% *include ([^ ]+) *%}/', function ($matches) {
            return '<?php self::load("'.str_replace('.', '/', $matches[1]).'"); ?>';
        }, $tpl_cont);
        $tpl_cont = preg_replace_callback('/{% *module (.+) *%}/', function ($matches) {
            return '<?php use pe\\modules\\'.str_replace('.', '\\', $matches[1]).'; ?>';
        }, $tpl_cont);
        // Parsing if statements
        $tpl_cont = preg_replace('/{% *(if|elseif) (.+?) *%}/', '<?php $1($2): ?>', $tpl_cont);
        $tpl_cont = preg_replace('/{% *else *%}/', '<?php else: ?>', $tpl_cont);
        $tpl_cont = preg_replace('/{% *endif *%}/', '<?php endif; ?>', $tpl_cont);
        // Parsing for statements
        $tpl_cont = preg_replace('/{% *for ([^,]+) *, *(.+)? in (.+) *%}/', '<?php foreach($3 as $1 => $2): ?>', $tpl_cont);
        $tpl_cont = preg_replace('/{% *for ([^,]+) * in (.+) *%}/', '<?php foreach($2 as $1): ?>', $tpl_cont);
        $tpl_cont = preg_replace('/{% *endfor *%}/', '<?php endforeach; ?>', $tpl_cont);
        // Parsing template variables
        $tpl_cont = preg_replace('/{{ *([^}]+) *}}/', '<?php echo $1; ?>', $tpl_cont);
        // Parsing language variables
        $tpl_cont = preg_replace('/\| *((?:-|\w)+) *\|/', '<?php echo self::lang("$1"); ?>', $tpl_cont);

        // Passing template variables
        global $vars, $_CONFIG;
        $vars = new ViewVarsStore($_vars);

        // Executing template
        ob_start();
        self::$last_tpl_cont = htmlspecialchars($tpl_cont);
        try {
        eval('
            namespace pe\\engine;
            global $vars;
            ?>
            '.$tpl_cont);
        } catch (\Exception $e) {
            // TODO: Noral view debugger
            exit($e);
        }
        $result = ob_get_clean();
        if(preg_match('/(Error|Notice|Warning):/i', trim($result))) {
            self::error(500, 'Error in executing template'.PHP_EOL.htmlspecialchars($tpl_cont).PHP_EOL.PHP_EOL.'Result:'.PHP_EOL.$result);
            exit;
        }
        
        // Saving parsed template to cache if it enabled
        if($_CONFIG['cache']) {
            $cache_path = CACHE.'/'.str_replace('/', '_', $tpl).'.php';
            if(!file_exists($cache_path)) {
                file_put_contents($cache_path, $tpl_cont);
            }
        }
        return $result;
    }
    
    private static $lang_data = [];
    public static function lang($lng_text) {
        if ($lng_text == '') return '||';
        if (self::$lang_data == []) {
            $lang_path = LANG.'/'.$_COOKIE['lng'].'.ini';
            if (file_exists($lang_path)) {
                self::$lang_data = parse_ini_file($lang_path);
            } else {
                self::error(500, '[Lang] Language with code "'.$_COOKIE['lng'].'" doesn`t exists');
            }
        }
        return isset(self::$lang_data[$lng_text])?self::$lang_data[$lng_text]:'none';
    }
    
    public static function get_languages() {
        $lang_codes = scandir(LANG);
        array_shift($lang_codes);
        array_shift($lang_codes);
        
        $result = [];
        foreach($lang_codes as $lang_code) {
            $result[str_replace('.ini', '', $lang_code)] = parse_ini_file(LANG.'/'.$lang_code)['language_name'];
        }
        return $result;
    }
}

class ViewVarsStore {
	private $vars = [];
	function __construct ($vars) { $this->vars = $vars; }
	function __isset ($var) { return isset($this->vars[$var]); }
	function __get ($var) { return $this->vars[$var]; }
}
