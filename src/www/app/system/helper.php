<?php

namespace App\System;

use \App\Entity\User;
use \ZCL\DB\DB as DB;

/**
 * Вспомагательный  класс  для  работы  с  бизнес-данными
 */
class Helper
{

    /**
     * Выполняет  логин  в  системму
     *
     * @param mixed $login
     * @param mixed $password
     * @return  boolean
     */
    public static function login($email, $password = null) {

        $user = User::findOne("  email='{$email}' ");

        if ($user == null)
            return false;
        if ($user->userpass == $password)
            return $user;
        if (strlen($password) > 0) {
            $b = password_verify($password, $user->userpass);
            return $b ? $user : false;
        }
        return false;
    }

    /**
     * Проверка  существования логина
     *
     * @param mixed $login
     */
    public static function existsLogin($email) {
        $list = \App\Entity\User::find("  email='{$email}' ");

        return count($list) > 0;
    }

    public static function loadEmail($template, $keys = array()) {
        global $logger;

        $templatepath = _ROOT . 'templates/email/' . $template . '.tpl';
        if (file_exists(strtolower($templatepath)) == false) {

            $logger->error($templatepath . " is wrong");
            return "";
        }
        $template = @file_get_contents(strtolower($templatepath));

        $m = new \Mustache_Engine();
        $template = $m->render($template, $keys);


        return $template;
    }

    public static function sendLetter($template, $email, $subject = "") {


        $_config = parse_ini_file(_ROOT . 'config/config.ini', true);


        $mail = new \PHPMailer();
        $mail->setFrom($_config['common']['emailfrom'], 'Биржа jobber');
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->msgHTML($template);
        $mail->CharSet = "UTF-8";
        $mail->IsHTML(true);


        $mail->send();
        /*

          $from_name = '=?utf-8?B?' . base64_encode("Биржа jobber") . '?=';
          $subject = '=?utf-8?B?' . base64_encode($subject) . '?=';
          mail(
          $email,
          $subject,
          $template,
          "From: " . $from_name." <{$_config['common']['emailfrom']}>\r\n".
          "Content-type: text/html; charset=\"utf-8\""
          );
         */
    }

    public static function fm($value) {
        return number_format($value / 100, 2, '.', '');
    }

}
