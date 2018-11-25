<?php
if (
    strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0')
 || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')
 || strpos($_SERVER['HTTP_USER_AGENT'], 'Edge')
 || strpos($_SERVER['HTTP_USER_AGENT'], 'Internet Explorer')
) {
    require_once 'errors/bad_browser.php';
    exit;
}
