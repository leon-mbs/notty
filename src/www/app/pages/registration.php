<?php

namespace App\Pages;

use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\DropDownChoice;
use \App\System\Helper;
use \App\Entity\User;
use \App\System\Application as App;
use \App\System\System;
use \Zippy\Binding\PropertyBinding as Bind;

class Registration extends Base
{

    public function __construct() {
        parent::__construct();

        $form = new \Zippy\Html\Form\Form('regform');
        $form->add(new TextInput('username'));
        $form->add(new TextInput('password'));
        $form->add(new TextInput('email'));
        $form->add(new TextInput('confirm'));
        $form->add(new DropDownChoice('userrole',array(User::ROLE_ORGANIZER=>'Organizer',User::ROLE_VENDOR=>'Vendor',User::ROLE_CLUBBER=>'Clubber')));
       
        $form->onSubmit($this, 'onsubmit');

        $this->add($form);

        $user = System::getUser();
        if ($user->user_id > 0) {
            App::Redirect("\\App\\Pages\\Main");
        }
    }

    public function onsubmit($sender) {
        $this->setError('');

        $confirm = $this->regform->confirm->getText();
        $password = $this->regform->password->getText();
        $email = $this->regform->email->getText();
        $username = $this->regform->username->getText();
         $role = $this->regform->userrole->getValue();


        if (!preg_match('/[A-Za-z0-9]+/', $password)) {
            $this->setError("Оnly latin and digit symbols are alдowed. ");
        } else
        if (strlen($password) < 6) {
            $this->setError("Password length must be at least 6 symbols");
        } else
        if ($confirm == '') {
            $this->setError("Confirm password ");
        } else
        if ($confirm != $password) {
            $this->setError("Confirm password ");
        } else
        if (Helper::existsLogin($email)) {
            $this->setError("Such email already exists");
        }else if($role ==0){
            $this->setError("Select role");
        }

        if (!$this->isError()) {
            $user = new User();
            $user->email = $email;

            $user->username = $username;

            $user->userpass = (\password_hash($password, PASSWORD_DEFAULT));
            $user->Save();
            System::setUser($user);

            if (\App\System\Session::getSession()->topage == null) {
                App::Redirect("\\App\\Pages\\UserProfile");
            } else {
                App::toPage(\App\System\Session::getSession()->topage);
            }
        }

        $this->regform->password->setText('');
        $this->regform->confirm->setText('');
    }

}
