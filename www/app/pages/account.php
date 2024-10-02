<?php

namespace App\Pages;

use \App\Application as App;
use \App\System;
use \App\Helper;
use \App\Entity\User;
use \Zippy\Html\Panel;
use \Zippy\Html\Label;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Link\RedirectLink;
use \Zippy\Html\Form\File;
use \Zippy\Html\Image;

class Account extends Base
{

    private $_user;

    public function __construct() {
        parent::__construct();
        System::checkLogined();
        $this->_user = System::getUser();

        $this->add(new Form('profileform'))->onSubmit($this, 'profileformOnSubmit');
        $this->profileform->add(new TextInput('username', $this->_user->username));
        

        $this->profileform->add(new TextInput('password'));
        $this->profileform->add(new TextInput('confirm'));


    }

    public function profileformOnSubmit($sender) {
        $this->setError('');

        $confirm = $this->profileform->confirm->getText();
        $password = $this->profileform->password->getText();

        $this->_user->username = $this->profileform->username->getText();

      

        if ($password != '') {


            if ($confirm == '') {
                $this->setError('Неверное подтверждение');
                return;
            } else
            if ($confirm != $password) {
                $this->setError('Неверное подтверждение');
                return;
            }
            $this->_user->userpass = (\password_hash($password, PASSWORD_DEFAULT));
               
        }
 

        $this->_user->save();
        System::setUser($this->_user);
     
        $this->setSuccess('Изменения сохранены');
  
        $this->profileform->password->setText('');
        $this->profileform->confirm->setText('');
    }

}
