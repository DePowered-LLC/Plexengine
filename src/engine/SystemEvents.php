<?php
/*
@copy
 */

namespace pe\engine;
class SystemEvents {
    private static $_triggers;
    public static function on ($event, $cb) {
        if (!isset(self::$_triggers[$event])) self::$_triggers[$event] = [];
        self::$_triggers[$event][] = $cb;
    }

	public static function emit ($event) {
        if (isset(self::$_triggers[$event])) {
            foreach (self::$_triggers[$event] as $cb) {
                $returned = call_user_func_array($cb, array_slice(func_get_args(), 1));
                if ($returned !== null && $returned !== false) return $returned;
            }
        }
    }

	public static function emitAll ($event) {
        if (isset(self::$_triggers[$event])) {
            $result = [];
            foreach (self::$_triggers[$event] as $cb) {
                $returned = call_user_func_array($cb, array_slice(func_get_args(), 1));
                if ($returned !== null && $returned !== false) $result[] = $returned;
            }
            return $result;
        }
    }
}
