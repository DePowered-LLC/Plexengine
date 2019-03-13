<?php
namespace pe\modules\System;

class StaticEventEmitter {
    private static $_triggers;
    public static function on ($event, $cb) {
        if (!isset(self::$_triggers[$event])) self::$_triggers[$event] = [];
        self::$_triggers[$event][] = $cb;
    }

	protected static function emit ($event) {
        if (isset(self::$_triggers[$event])) {
            foreach (self::$_triggers[$event] as $cb) {
                $returned = call_user_func_array($cb, array_slice(func_get_args(), 1));
                if ($returned !== null && $returned !== false) return $returned;
            }
        }
    }
}
