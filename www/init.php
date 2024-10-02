<?php

error_reporting(E_ALL & ~E_WARNING & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);


$http = 'http';
if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
    $http = 'https';
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $http = 'https';
} elseif (443 == intval($_SERVER['SERVER_PORT'])) {
    $http = 'https';
}

define('_BASEURL', $http . "://" . $_SERVER["HTTP_HOST"] . '/');

define('_ROOT', __DIR__ . '/');


//чтение  конфигурации

require_once _ROOT . 'config/config.php';

 
if (!is_array($_config)) {
    die("Invalid config file");
}

date_default_timezone_set($_config['common']['timezone'] ?? 'Europe/Kiev');


require_once _ROOT . 'vendor/autoload.php';
include_once _ROOT . "vendor/adodb/adodb-php/adodb-exceptions.inc.php";


// логгер
$logger = new \Monolog\Logger("main");

$level = $_config['common']['loglevel'];

$output = "%datetime%  %level_name% : %message% \n";
$formatter = new \Monolog\Formatter\LineFormatter($output, "Y-m-d H:i:s");
$h1 = new \Monolog\Handler\RotatingFileHandler(_ROOT . "logs/app.log", 5, $level);
$h2 = new \Monolog\Handler\RotatingFileHandler(_ROOT . "logs/error.log", 5, \Monolog\Logger::ERROR);
$h1->setFormatter($formatter);
$h2->setFormatter($formatter);
$logger->pushHandler($h1);
$logger->pushHandler($h2);
$logger->pushProcessor(new \Monolog\Processor\IntrospectionProcessor());

 
 

//Параметры   соединения  с  БД
\ZDB\DB::config($_config['db']['host'], $_config['db']['name'], $_config['db']['user'], $_config['db']['pass']);


//проверяем соединение
try {
    $conn = \ZDB\DB::getConnect();
} catch(Throwable $e) {
    echo 'Ошибка  соединения с  БД. Подробности  в папке logs';

    $logger->error($e);
    die;
}

// автолоад классов  приложения
function app_autoload($className) {
    $className = str_replace("\\", "/", ltrim($className, '\\'));

    if (strpos($className, 'App/') === 0) {
        $file = __DIR__ . DIRECTORY_SEPARATOR . strtolower($className) . ".php";
        if (file_exists($file)) {
            require_once $file;
        } else {
            die('Неверный класс ' . $className);
        }
    }
}

spl_autoload_register('app_autoload');

session_start();


if (!function_exists('mb_ucfirst') && function_exists('mb_substr')) {

    function mb_ucfirst($string) {
        $string = mb_ereg_replace("^[\ ]+", "", $string);
        $string = mb_strtoupper(mb_substr($string, 0, 1, "UTF-8"), "UTF-8") . mb_substr($string, 1, mb_strlen($string), "UTF-8");
        return $string;
    }

}

