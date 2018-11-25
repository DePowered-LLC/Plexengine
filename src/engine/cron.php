<?php
require_once 'db.php';
$_CONFIG = parse_ini_file(dirname(__DIR__).'/data/config.ini');
DB::connect($_CONFIG['mysql_host'], $_CONFIG['mysql_user'], $_CONFIG['mysql_pass'], $_CONFIG['mysql_db']);

DB::update('users', [
    'limitation' => ''
], [
    'SUBSTRING_INDEX(limitation, ";", -1) <= '.time()
]);

// TODO: auto kick guests
// DB::update('users', [
//     'limitation' => ''
// ], [
//     'SUBSTRING_INDEX(limitation, ";", -1) <= '.time()
// ]);
