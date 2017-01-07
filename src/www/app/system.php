<?php

namespace App;

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
    public static function getUser()
    {
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
    public static function setUser(User $user)
    {
        Session::getSession()->user_id = $user->user_id;
    }

    /**
     * Возвращает  сессию
     * @return  Session
     */
    public static function getSession()
    {

        return Session::getSession();
    }

    /**
     * Возвращает набор  параметром  по  имени набора

     */
    public static function getOptions()
    {


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
    public static function setOptions($options)
    {

        $conn = \ZCL\DB\DB::getConnect();
        foreach ($options as $key => $value) {
            $conn->Execute(" delete from  options where  optname='{$key}' ");
            $conn->Execute(" insert into options (optname,optvalue) values ( '{$key}', '{$value}') ");
        }
    }

    /**
     * Проверка  залогинени  ли  пользователь
     *
     * @param mixed $role  требуемая  роль
     * @param mixed $uri  адрес  перехода после  логина
     */
    public static function checkLogined($role = -1, $uri = null)
    {
        $user = System::getUser();
        if ($user->user_id == 0) {
            \App\System\Session::getSession()->topage = $uri;
            Application::toPage("/signin");
        }
        if ($role >= 0 && $user->userrole != $role && $user->userrole != User::ROLE_ADMIN) {
            Application::toPage('/');
        }
        \App\Session::getSession()->topage = null;
    }

}
