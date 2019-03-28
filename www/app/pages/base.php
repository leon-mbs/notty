<?php

namespace App\Pages;

use \App\Application as App;
use \App\System;
use \App\Helper;
use \App\Entity\User;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Link\RedirectLink;
use \Zippy\Html\Panel;
use \Zippy\Html\Label;

class Base extends \Zippy\Html\WebPage
{

    

    public function __construct() {
        parent::__construct();

      


        $this->add(new ClickLink("logout", $this, OnExit));

        $user = System::getUser();

        if ($_COOKIE['remember'] && $user->user_id == 0) {
            $arr = explode('_', $_COOKIE['remember']);
            $_config = parse_ini_file(_ROOT . 'config/config.ini', true);
            if ($arr[0] > 0 && $arr[1] === md5($arr[0] . $_config['common']['salt'])) {
                $user = User::load($arr[0]);
            }

            if ($user instanceof User) {


                System::setUser($user);
            }
        }
        $user = System::getUser();
        if ($user->user_id == 0) {
            if ($this instanceof UserLogin) {
                
            } else {
                App::Redirect("\\App\\Pages\\UserLogin");
            }
        }

        $this->_tvars["username"] = $user->user_id == 0 ? "" : $user->username;
        $this->_tvars["admin"] = $user->username == 'admin';
    }

    public function OnExit($sender) {

        setcookie("remember", '', 0, '/');
        System::setUser(new \App\Entity\User());
        $this->_tvars["username"] = "";
        App::Redirect("\\App\\Pages\\UserLogin");
    }

    //вывод ошибки,  используется   в дочерних страницах
    public function setError($msg) {
        System::setErrorMsg($msg);
    }

    public function setSuccess($msg) {
        System::setSuccesMsg($msg);
    }

    public function setWarn($msg) {
        System::setWarnMsg($msg);
    }

    public function setInfo($msg) {
        System::setInfoMsg($msg);
    }

    final protected function isError() {
        return strlen(System::getErrorMsg()) > 0;
    }

    protected function beforeRender() {
   }

    protected function afterRender() {
        if (strlen(System::getErrorMsg()) > 0)
            App::$app->getResponse()->addJavaScript("toastr.error('" . System::getErrorMsg() . "')        ", true);
        if (strlen(System::getWarnMsg()) > 0)
            App::$app->getResponse()->addJavaScript("toastr.warning('" . System::getWarnMsg() . "')        ", true);
        if (strlen(System::getSuccesMsg()) > 0)
            App::$app->getResponse()->addJavaScript("toastr.success('" . System::getSuccesMsg() . "')        ", true);
        if (strlen(System::getInfoMsg()) > 0)
            App::$app->getResponse()->addJavaScript("toastr.info('" . System::getInfoMsg() . "')        ", true);



        $this->setError('');
        $this->setSuccess('');

        $this->setInfo('');
        $this->setWarn('');
    }

     
}
