<?php

namespace App\Entity;

use \ZCL\DB\Entity;

/**
 *  Класс  инкапсулирующий   сущность  пользователь
 * @table=users
 * @view=usersview
 * @keyfield=user_id
 */
class User extends Entity
{

    const ROLE_GUEST = 0;
    const ROLE_ADMIN = 1;
    const ROLE_CLUBBER = 2;
    const ROLE_VENDOR = 3;
    const ROLE_ORGANIZER = 4;
    const STATE_ACTIVE = 0;
    const STATE_DISABLED = 1;
    const STATE_NEW = 2;

    /**
     * @see Entity
     *
     */
    protected function init()
    {

        $this->user_id = 0;
        $this->userrole = 0;

        $this->createdon = time();

        $this->avatar = '/assets/img/noimage.jpg';
    }

    /**
     * Проверка  залогинивания
     *
     */
    public function isLogined()
    {
        return $this->user_id > 0;
    }

    /**
     * Выход из  системмы
     *
     */
    public function logout()
    {
        $this->init();
    }

    /**
     * Возвращает  пользователя   по  логину
     *
     * @param mixed $login
     */
    public static function getByEmail($email)
    {
        $conn = \ZCL\DB\DB::getConnect();
        return User::getFirst('email = ' . $conn->qstr($email));
    }

    /**
     * Возвращает ID  пользователя
     *
     */
    public function getUserID()
    {
        return $this->user_id;
    }

    protected function beforeSave()
    {
        parent::beforeSave();


        //упаковываем  данные в detail
        $this->details = "<detail>";

        $this->details .= "<vendorresume><![CDATA[{$this->vendorresume}]]></vendorresume>";

        $this->details .= "</detail>";

        return true;
    }

    protected function afterLoad()
    {

        $this->createdon = strtotime($this->createdon);
        $this->lastlogin = strtotime($this->lastlogin);

        if (strlen($this->details) > 0) {
           
            $xml = simplexml_load_string($this->details);

            $this->vendorresume = (string) ($xml->vendorresume[0]);
     
        }

        parent::afterLoad();
    }

   
    public function rolename()
    {
       

        if ($this->userrole == self::ROLE_ADMIN)
            return "Admin,";
        if ($this->userrole == self::ROLE_CLUBBER)
            return "Clubber,";
        if ($this->userrole == self::ROLE_VENDOR)
            return "Vendor,";
        if ($this->userrole == self::ROLE_ORGANIZER)
            return "Organizer,";

         
    }

     

}
