<?php
if (strpos($_SERVER['REQUEST_URI'], 'index.php') > 1) {
    die('Сайт размещен не в корневой директории');
}
require_once 'init.php';

try {
    $user = null;

    if (($_COOKIE['remember'] ?? false) && \App\System::getUser()->user_id == 0) {
        $arr = explode('_', $_COOKIE['remember']);

        if ($arr[0] > 0 && $arr[1] === md5($arr[0] . \App\Helper::getSalt())) {
            $user = \App\Entity\User::load($arr[0]);
        }

        if ($user instanceof \App\Entity\User) {
            \App\Session::getSession()->clean();
            \App\System::setUser($user);
        }
    }
    $app = new \App\Application();

    $app->Run('\App\Pages\Main');

} catch (Exception $e) {
    if ($e instanceof ADODB_Exception) {
        \ZCL\DB\DB::getConnect()->CompleteTrans(false); // откат транзакции
    }
    $msg = $e->getMessage();
    $logger->error($e->getMessage(), $e->getTrace());
    \App\Application::Redirect('\\App\\Pages\\Error', $e->getMessage());
}
