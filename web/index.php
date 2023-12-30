<?php
ini_set('date.timezone', 'Asia/Dhaka');

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';

EzMaintenance\Worker::watch('file', array(
    'path' => 'maintenance.enable',
    'template' => 'game',
    /* 'template' => 'under_construction.php',*/
    'msg' => 'Site is currently undergoing maintenance!'
));

defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'prod'));

define('WEB_PATH', __DIR__);

$loader = require_once __DIR__ . '/../app/bootstrap.php.cache';

if (APPLICATION_ENV == 'dev') {
    require 'app_dev.php';
} else {
    require 'app.php';
}


