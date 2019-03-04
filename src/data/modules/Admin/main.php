<?php
/*
@copy
 */

namespace pe\modules\Admin;
use pe\engine\Router;

class Main {
    public function __construct () {
        // if(!isset($_SESSION['userdata']) || $_SESSION['userdata']['access'] != 'admin') {
        //     View::error(403, 'No admin access');
        //     exit;
        // }

        Router::add('get', '/admin', 'IndexRequests.index');
        Router::module('get', '/admin/<action>', 'IndexRequests');
    }
}