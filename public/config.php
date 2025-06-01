<?php
ob_start();
//Create session per user:
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('DB_TYPE')) define('DB_TYPE', 'mysql');
if (!defined('DB_HOST')) define('DB_HOST', 'db');
if (!defined('DB_PORT')) define('DB_PORT', '3306');

if (!defined('DB_NAME')) define('DB_NAME', 'auth');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', 'root');

if (!defined('ROOT_PATH')) define('ROOT_PATH', realpath(dirname(__FILE__)));
if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost:8080/');



