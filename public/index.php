<?php
date_default_timezone_set('PRC');
header('Content-type: text/html; charset=utf-8');
define('DS', '/');
define('PUBLIC_PATH', dirname(__FILE__).DS);
define('BASE_PATH', realpath(dirname(__FILE__).DS.'..').DS);
define('APP_PATH', realpath(dirname(__FILE__).DS.'..'.DS.'application').DS);

define('APPLICATION_PATH', dirname(__FILE__) . '/../');
define('BASE_URL',"http://localhost:8082/");

// composer
require_once BASE_PATH.'vendor/autoload.php';

$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();

