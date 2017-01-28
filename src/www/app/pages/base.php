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

    public $_errormsg;
    public $_successmsg;
    public $_warnmsg;
    public $_infomsg;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Label("errormessage", new \Zippy\Binding\PropertyBinding($this, '_errormsg'), false, true))->setVisible(false);
        $this->add(new Label("successmessage", new \Zippy\Binding\PropertyBinding($this, '_successmsg'), false, true))->setVisible(false);
        $this->add(new Label("warnmessage", new \Zippy\Binding\PropertyBinding($this, '_warnmsg'), false, true))->setVisible(false);
        $this->add(new Label("infomessage", new \Zippy\Binding\PropertyBinding($this, '_infomsg'), false, true))->setVisible(false);



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
            App::Redirect("\\App\\Pages\\UserLogin");
        }

        $this->_tvars["username"] = $user->user_id == 0 ? "" : $user->username;
        $this->_tvars["admin"] = $user->username == 'admin' ;
    }

    public function OnExit($sender)
    {

        setcookie("remember", '', 0, '/');
        System::setUser(new \App\Entity\User());
        $this->_tvars["username"] = "";
        App::Redirect("\\App\\Pages\\UserLogin");
    }

    public function setError($msg)
    {
        $this->_errormsg = $msg;
        //    $this->errormessage->setVisible(strlen($msg) > 0);
    }

    public function setSuccess($msg)
    {
        $this->_successmsg = $msg;
    }

    public function setInfo($msg)
    {
        $this->_infomsg = $msg;
    }

    public function setWarn($msg)
    {
        $this->_warnmsg = $msg;
    }

    protected function beforeRender()
    {
        $this->errormessage->setVisible(strlen($this->_errormsg) > 0);
        $this->successmessage->setVisible(strlen($this->_successmsg) > 0);
        $this->infomessage->setVisible(strlen($this->_infomsg) > 0);
        $this->warnmessage->setVisible(strlen($this->_warnmsg) > 0);
    }

    protected function afterRender()
    {
        $this->setError('');
        $this->setSuccess('');
        $this->setInfo('');
        $this->setWarn('');
    }

    protected function isError()
    {
        return strlen($this->_errormsg) > 0 ? true : false;
    }

}
