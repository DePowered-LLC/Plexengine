<?php
namespace pe\engine;
use PDO;

class DB {
    private static $connection;
    public static $insert_id;
    
    public static function connect($host, $user, $pass, $db) {
        try {
            self::$connection = new PDO('mysql:dbname='.$db.';host='.$host, $user, $pass);
        } catch(PDOException $e) {
            return $e;
        }
    }
    
    public static function count($table, $field, $opts = '') {
        $where = self::where_parser($opts);
        $query = 'SELECT COUNT(`'.addcslashes($field, '`\\').'`) FROM `'.addcslashes($table, '`\\').'`'.$where;
        $result = self::$connection->query($query)->fetch()[0];
        return $result;
    }
    
    public static function delete($table, $opts = '') {
        $where = self::where_parser($opts);
        $query = 'DELETE FROM `'.addcslashes($table, '`\\').'`'.$where;
        $result = self::$connection->query($query);
    }
    
    public static function find($table, $opts = '') {
        $where = self::where_parser($opts);
        $query = 'SELECT * FROM `'.addcslashes($table, '`\\').'`'.$where;
        $result = self::$connection->query($query)->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public static function find_first($table, $opts) {
        $result = self::find($table, $opts);
        return isset($result[0])?$result[0]:[];
    }
    
    private static function where_parser($opts) {
        $where = '';
        if($opts != '' && $opts[0] != '' && is_array($opts)) {
            $where = ' WHERE '.$opts[0];
            while(strpos($where, ':') !== false) {
                $where_temp = explode(':', $where);
                $where = str_replace(':'.$where_temp[1].':', "'".addcslashes($opts['bind'][$where_temp[1]], '\'\\')."'", $where);
            }
        } elseif(is_string($opts)) {
            $where = ' '.$opts;
        }
        return $where;
    }
    
    public static function update($table, $data, $opts) {
        $where = self::where_parser($opts);
        $set = '';
        if(is_string($data)) $set = $data;
        else {
            foreach($data as $key => $val) {
                if($set != '') { $set .= ', '; }
                $set .= "`".addcslashes($key, '`\\')."`='".addcslashes($val, '\'\\')."'";
            }
        }
        $query = 'UPDATE `'.addcslashes($table, '`\\').'` SET '.$set.$where;
        self::$connection->query($query);
    }
    
    public static function insert($table, $data) {
        $cols = '';
        $vals = '';
        foreach($data as $key => $val) {
            if($cols != '') { $cols .= ', '; }
            $cols .= '`'.addcslashes($key, '`\\').'`';
            if($vals != '') { $vals .= ', '; }
            if($val === null) { $vals .= "NULL"; }
            else { $vals .= "'".addcslashes($val, '\'\\')."'"; }
        }
        $query = 'INSERT INTO `'.addcslashes($table, '`\\').'` ('.$cols.') VALUES('.$vals.')';
        self::$connection->query($query);
        return self::$insert_id = self::$connection->lastInsertId();
    }

    // Models
    public static function model ($table, $scheme) {
        $table = addcslashes($table, '\'\\');
        $is_exists = self::$connection->query("SELECT 1 FROM `$table`");
        if (!$is_exists) {
            $fields = [];
            foreach ($scheme as $key => $desk) {
                $key = addcslashes($key, '\'\\');
                $fields[] = '`'.$key.'` '.self::parseField($key, $desk);
            }

            $fields = implode(',', $fields);
            $query = "CREATE TABLE `$table` ($fields) CHARSET=utf8 AUTO_INCREMENT=1";
            self::$connection->query($query);
        }
    }

    public static function parseField ($key, $desk) {
        if (is_array($desk)) {
            $result = $desk['type'].' NOT NULL';
            if (isset($desk['increment']) && $desk['increment']) $result .= ' AUTO_INCREMENT';
            if (isset($desk['unique']) && $desk['unique']) $result .= ", UNIQUE KEY `$key` (`$key`)";
            return $result;
        } else return $desk.' NOT NULL';
    }
}
