<?php
namespace pe\engine;
class Utils {
    private static $is_case_sensitive = false;
    private static $is_utf = false;

    public static function check ($fields) {
        foreach ($fields as $field => $filter) {
            if (!isset($_POST[$field])) {
                return $field.'_empty';
            } else {
                $val = $_POST[$field];
                if ($filter['type'] == 'string') {
                    if (trim($val) == '') return $field.'_empty';
                    self::$is_case_sensitive = isset($filter['case_sens']) && $filter['case_sens'];
                    self::$is_utf = isset($filter['utf']) && $filter['utf'];
                    if (isset($filter['max']) && self::length($val, $filter['utf']) > $filter['max']) return $field.'_wrong_length';
                    if (isset($filter['min']) && self::length($val, $filter['utf']) < $filter['min']) return $field.'_wrong_length';
                    if (isset($filter['value']) && !self::eqeq($val, $filter['value'])) return $field.'_not_valid';
                }
            }
        }
        return 'success';
    }

    public static function length ($str) {
        return self::$is_utf ? mb_strlen($str, 'UTF-8') : strlen($str);
    }

    public static function eqeq ($str1, $str2) {
        if (!self::$is_case_sensitive) {
            if (self::$is_utf) {
                $str1 = mb_strtolower($str1, 'UTF-8');
                $str2 = mb_strtolower($str2, 'UTF-8');
            } else {
                $str1 = strtolower($str1);
                $str2 = strtolower($str2);
            }
        }
        return $str1 == $str2;
    }
}
