<?php

namespace App\System;

use \App\Entity\User;

/**
 * Класс  содержащи  методы  работы   с  наиболее  важными
 * системмными  данными
 */
class System
{

    /**
     * Возвращает  текущего  юзера
     * @return  User
     */
    public static function getUser() {
        $user = Session::getSession()->user;
        if ($user instanceof User) {
            
        } else {
            $user = new User();
            self::setUser($user);
        }
        return $user;
    }

    /**
     * Устанавливавет  текущего  юзера  в  системме
     *
     * @param User $user
     */
    public static function setUser(User $user) {
        Session::getSession()->user = $user;
    }

    /**
     * Возвращает  сессию
     * @return  Session
     */
    public static function getSession() {

        return Session::getSession();
    }

    /**
     * Возвращает набор  параметром  по  имени набора

     */
    public static function getOptions() {


        $conn = \ZCL\DB\DB::getConnect();
        $options = array();
        $rs = $conn->Execute("select optname,optvalue from  options  ");
        foreach ($rs as $row) {
            $options[$row['optname']] = $row['optvalue'];
        }

        return $options;
    }

    /**
     * Записывает набор  параметров  по имени набора
     *
     * @param mixed $group
     * @param mixed $options
     */
    public static function setOptions($options) {

        $conn = \ZCL\DB\DB::getConnect();
        foreach ($options as $key => $value) {
            $conn->Execute(" delete from  options where  optname='{$key}' ");
            $conn->Execute(" insert into options (optname,optvalue) values ( '{$key}', '{$value}') ");
        }
    }

    /**
     * Проверка  залогинени  ли  пользователь
     *
     * @param mixed $role  требуемая  роль или массив  ролей
     * @param mixed $uri  адрес  перехода после  логина
     */
    public static function checkLogined($role = null, $uri = "/") {

        $user = System::getUser();
        if ($user->user_id == 0) {
            \App\System\Session::getSession()->topage = $uri;
            Application::toPage("/signin");
        }
        \App\System\Session::getSession()->topage = null;

        if ($role == null)
            return true;

        if (!is_array($role)) {
            $role = array($role);
        }


        foreach ($role as $r) {
            if ($role >= 0 && $user->userrole == $r) {
                return;
            }
        }

        Application::toPage('/');
    }

}
