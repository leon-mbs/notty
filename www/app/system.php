<?php

namespace App;

use \App\Entity\User;

/**
 * Класс  содержащи  методы  работы   с  наиболее  важными
 * системмными  данными
 */
class System
{
    public const CURR_VERSION = "2.0.0";
  
    /**
     * Возвращает  текущего  юзера
     * @return  User
     */
    public static function getUser() {
        $user_id = Session::getSession()->user_id;
        if ($user_id == null) {
            $user = new User();
            self::setUser($user);
            return $user;
        }
        return User::load($user_id);
    }

    /**
     * Устанавливавет  текущего  юзера  в  системме
     *
     * @param User $user
     */
    public static function setUser(User $user) {
        Session::getSession()->user_id = $user->user_id;
    }

    /**
     * Возвращает  сессию
     * @return  Session
     */
    public static function getSession() {

        return Session::getSession();
    }
 

    /**
     * Проверка  залогинени  ли  пользователь
     *
     * @param mixed $role  требуемая  роль
     * @param mixed $uri  адрес  перехода после  логина
     */
    public static function checkLogined($role = -1, $uri = null) {
        $user = System::getUser();
        if ($user->user_id == 0) {
            \App\System\Session::getSession()->topage = $uri;
            Application::RedirectURI("/signin");
        }
        if ($role >= 0 && $user->userrole != $role && $user->userrole != User::ROLE_ADMIN) {
            Application::RedirectURI('/');
        }
        \App\Session::getSession()->topage = null;
    }

    
    public static function setSuccessMsg($msg) {
      $msg = str_replace("'", "`", $msg) ;
      Session::getSession()->smsg = $msg;
    }

    public static function getSuccesMsg() {
        return Session::getSession()->smsg;
    }


    public static function setErrorMsg($msg ) {
        $msg = str_replace("'", "`", $msg) ;
        Session::getSession()->emsg = $msg;
    }

    public static function getErrorMsg() {
        return Session::getSession()->emsg;
    }
  
    public static function setWarnMsg($msg) {
        $msg = str_replace("'", "`", $msg) ;
        Session::getSession()->wmsg = $msg;
    }

    public static function getWarnMsg() {
        return Session::getSession()->wmsg;
    }

    public static function setInfoMsg($msg) {
       $msg = str_replace("'", "`", $msg) ;
       Session::getSession()->imsg = $msg;
    }

    public static function getInfoMsg() {
        return Session::getSession()->imsg;
    }
    
    
}
