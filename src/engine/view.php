<?php
/*
 * Plexengine
 * (C) DePowered LLC & Plexengine 2018
 * All rights reserved
 */
// ob_start();
class View {
    public static function error($err, $debug = '') {
        // ob_end_clean();
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
            require_once 'errors/'.$err.'.php';
        }
        exit;
    }
    
    public static function load($tpl) {
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
                echo self::parse($tpl_cont, $tpl);
            } else {
                self::error(404, '[Template] '.$tpl_path);
            }
        }
    }
    
    private static $last_tpl_cont = '';
    private static function parse($tpl_cont, $tpl) {
        $tpl_cont_part = explode('{%', $tpl_cont);
        array_shift($tpl_cont_part);
        foreach($tpl_cont_part as $func_str) {
            $result = '';
            $func_str = explode('%}', $func_str)[0];
            $func_args = explode(' ', trim($func_str));
            switch($func_args[0]) {
                case 'include':
                    $inc_path = str_replace('\'', '', $func_args[1]);
                    $inc_path = addcslashes($inc_path, '"\\');
                    $result = 'self::load("'.$inc_path.'");';
                    break;
                case 'if':
                    array_shift($func_args);
                    $if = implode(' ', $func_args);
                    $result = 'if('.$if.'):';
                    break;
                case 'else':
                    $result = 'else:';
                    break;
                case 'endif':
                    $result = 'endif;';
                    break;
                case 'for':
                    array_shift($func_args);
                    $st = implode(' ', $func_args);
                    
                    $in_var = trim(explode('in', $st)[1]);
                    
                    $out_vars = explode(',', explode('in', $st)[0]);
                    foreach($out_vars as $key => $val) {
                        $out_vars[$key] = trim($val);
                    }
                    $out_vars = implode(' => ', $out_vars);
                    
                    $result = 'foreach('.$in_var.' as '.$out_vars.'):';
                    break;
                case 'endfor':
                    $result = 'endforeach;';
                    break;
            }
            $tpl_cont = str_replace('{%'.$func_str.'%}', '<?php '.$result.' ?>', $tpl_cont);
        }
        
        $tpl_cont_part = explode('{{', $tpl_cont);
        array_shift($tpl_cont_part);
        foreach($tpl_cont_part as $part) {
            $part = explode('}}', $part)[0];
            $tpl_cont = str_replace('{{'.$part.'}}', '<?php echo '.trim($part).'; ?>', $tpl_cont);
        }
        
        $tpl_cont_part = explode('{#', $tpl_cont);
        array_shift($tpl_cont_part);
        foreach($tpl_cont_part as $part) {
            $part = explode('#}', $part)[0];
            $tpl_cont = str_replace('{#'.$part.'#}', '', $tpl_cont);
        }
        
        global $_CONFIG;
        $tpl_cont_part = explode($_CONFIG['lang_delimiters'][0], $tpl_cont);
        array_shift($tpl_cont_part);
        foreach($tpl_cont_part as $key => $part) {
            if($key & 1) { continue; }
            $part = explode($_CONFIG['lang_delimiters'][1], $part)[0];
            $tpl_cont = str_replace($_CONFIG['lang_delimiters'][0].$part.$_CONFIG['lang_delimiters'][1], '<?php echo self::lang("'.trim($part).'"); ?>', $tpl_cont);
        }
        
        ob_start();
        self::$last_tpl_cont = htmlspecialchars($tpl_cont);
        eval('?>'.$tpl_cont);
        $result = ob_get_clean();
        if(preg_match('/(Error|Notice|Warning):/i', trim($result))) {
            self::error(500, 'Error in executing template'.PHP_EOL.htmlspecialchars($tpl_cont).PHP_EOL.PHP_EOL.'Result:'.PHP_EOL.$result);
            exit;
        }
        
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