<?php

namespace App\Pages;

 
use \Zippy\Html\Form\TextInput as TextInput;
use \App\Application as App;
use \App\Helper;
use \App\System;
use \App\Entity\User;
use \Zippy\Html\Label;

class UserLogin extends \Zippy\Html\WebPage
{

    public $_errormsg;
 

    public function __construct() {
        parent::__construct();

        $form = $this->add(new \Zippy\Html\Form\Form('loginform'));
        $form->onSubmit($this, 'onSubmit') ;
        $form->add(new TextInput('userlogin' ));
        $form->add(new TextInput('userpassword' ));
        $form->add(new \Zippy\Html\Form\CheckBox('remember'));
     
        
    }

    public function onsubmit($sender) {
        global $logger, $_config;        
        
        $login = trim( $sender->userlogin->getText() );
        $password = trim($sender->userpassword->getText() );
        
        $this->setError('');
        if ($login == '') {
            $this->setError('Введите логин');
        } else
        if ($password == '') {
            $this->setError('Введите пароль');
        }

        if (strlen($login) > 0 && strlen($password)) {

            $user = Helper::login($login, $password);

            if ($user instanceof User) {
              
                System::setUser($user);
                if ($sender->remember->isChecked()) {
                    
                    setcookie("remember", $user->user_id . '_' . md5($user->user_id . Helper::getSalt()), time() + 60 * 60 * 24 * 30);
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

        $sender->userpassword->setText('');
    }

    public function setError($msg) {
        $this->_errormsg = $msg;
    }

    protected function afterRender() {
        $this->_tvars['alerterror'] = false;
        if (strlen($this->_errormsg) > 0) {
           $this->_tvars['alerterror'] = $this->_errormsg;
        }
    }

}
