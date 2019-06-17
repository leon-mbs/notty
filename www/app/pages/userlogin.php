<?php

namespace App\Pages;

use \Zippy\Binding\PropertyBinding as Bind;
use \Zippy\Html\Form\TextInput as TextInput;
use \App\Application as App;
use \App\Helper;
use \App\System;
use \App\Entity\User;
use \Zippy\Html\Label;

class UserLogin extends \Zippy\Html\WebPage
{

    public $_errormsg;
    public $_login, $_password;

    public function __construct() {
        parent::__construct();

        $form = new \Zippy\Html\Form\Form('loginform');
        $form->add(new TextInput('userlogin', new Bind($this, '_login')));
        $form->add(new TextInput('userpassword', new Bind($this, '_password')));
        $form->add(new \Zippy\Html\Form\CheckBox('remember'));
        $form->add(new \Zippy\Html\Form\SubmitButton('submit'))->onClick($this, 'onsubmit');

        $this->add($form);
    }

    public function onsubmit($sender) {
       global $logger, $_config;        
        
        $this->setError('');
        if ($this->_login == '') {
            $this->setError('Введите логин');
        } else
        if ($this->_password == '') {
            $this->setError('Введите пароль');
        }

        if (strlen($this->_login) > 0 && strlen($this->_password)) {

            $user = Helper::login($this->_login, $this->_password);

            if ($user instanceof User) {
                $user->lastlogin = time();
                $user->save();
                System::setUser($user);
                $_SESSION['user_id'] = $user->user_id; //для  использования  вне  Application
                $_SESSION['userlogin'] = $user->userlogin; //для  использования  вне  Application
                //App::$app->getResponse()->toBack();
                if ($this->loginform->remember->isChecked()) {
                    
                    setcookie("remember", $user->user_id . '_' . md5($user->user_id . $_config['common']['salt']), time() + 60 * 60 * 24 * 30);
                }
                if (\App\Session::getSession()->topage == null) {
                    App::RedirectHome();
                } else {
                    App::RedirectURI(\App\Session::getSession()->topage);
                }
            } else {
                $this->setError('Неверный  логин');
            }
        }

        $this->_password = '';
    }

    public function setError($msg) {
        $this->_errormsg = $msg;
    }

    protected function afterRender() {

        if (strlen($this->_errormsg) > 0)
            App::$app->getResponse()->addJavaScript("toastr.error('{$this->_errormsg}')        ", true);

        $this->setError('');
    }

}
