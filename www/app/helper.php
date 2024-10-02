<?php

namespace App;

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
    public static function login($login, $password = null) {

        $user = User::getFirst("  userlogin='{$login}' ");

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

 

    public static function setKeyVal($key, $data = null) {
        if(strlen($key) == 0) {
            return;
        }
        $conn = \ZDB\DB::getConnect();
        $conn->Execute("delete  from  keyval  where  keyd=" . $conn->qstr($key));
        if($data === null) {
            return;
        }
        $conn->Execute("insert into keyval  (  keyd,vald)  values (" . $conn->qstr($key) . "," . $conn->qstr($data) . ")");


    } 
    
    public static function getKeyVal($key, $def = "") {
        if(strlen($key) == 0) {
            return;
        }
        $conn = \ZDB\DB::getConnect();

        $ret = $conn->GetOne("select vald from  keyval  where  keyd=" . $conn->qstr($key));

        if(strlen($ret) == 0) {
            $ret = "";
        }

        if($ret == '' && $def != '') {
            $ret = $def;
        }

        return $ret;
    }   
    
  //"соль" для  шифрования
    public static function getSalt() {
        $salt = self::getKeyVal('salt');
        if(strlen($salt ?? '') == 0) {
            $salt = '' . rand(1000, 999999);
            self::setKeyVal('salt', $salt);
        }
        return $salt;
    }
     
  public static function addFile($file, $topic_id) {
   
       
        $f = new  \App\Entity\File();
        $f->topic_id = $topic_id;
        $f->filename = $file['name'];
        $f->content = file_get_contents($file['tmp_name']);
        $f->save();
 
    }

    public static function deleteFile($file_id) {
        $conn = \ZDB\DB::getConnect();
        $conn->Execute("delete  from  files  where  file_id={$file_id}");

    }

    public static function findFileByTopic($topic_id) {
        $list= \App\Entity\File::find("topic_id=".$topic_id) ;

        $ret = array();
        foreach ($list as $f) {
           
            $f->content=null;;

            $ret[] =  $f;
        }

        return $ret;
    }
        
}
