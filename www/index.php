<?php

require_once 'init.php';

try {

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
