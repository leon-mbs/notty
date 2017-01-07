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

    public function __construct()
    {
        parent::__construct();
        System::checkLogined();
        $this->_user = System::getUser();

        $this->add(new Form('profileform'))->onSubmit($this, 'profileformOnSubmit');
        $this->profileform->add(new TextInput('username', $this->_user->username));
        $this->profileform->add(new TextInput('email', $this->_user->email));
        $this->profileform->add(new TextInput('phone', $this->_user->phone));
        $this->profileform->add(new DropDownChoice('city', Helper::getCityList(), $city_id > 0 ? $city_id : System::getUser()->city_id));

        $this->profileform->add(new TextInput('password'));
        $this->profileform->add(new TextInput('confirm'));
        $this->profileform->add(new File('avatar'));
        $this->profileform->add(new Image('avatarprev'));
        $this->profileform->avatarprev->setUrl($this->_user->avatar);

        $this->_tvars['notadmin'] = $this->_user->userrole != User::ROLE_ADMIN;
    }

    public function profileformOnSubmit($sender)
    {
        $this->setError('');

        $confirm = $this->profileform->confirm->getText();
        $password = $this->profileform->password->getText();

        $username = $this->profileform->username->getText();

        if ($username == '') {
            $this->setError('Введите имя');
        }


        if ($password != '') {


            if ($confirm == '') {
                $this->setError('Неверное подтверждение');
            } else
            if ($confirm != $password) {
                $this->setError('Неверное подтверждение');
            }
        }



        $file = $this->profileform->avatar->getFile();
        if (strlen($file['tmp_name']) > 0) {

            $imagedata = @getimagesize($file['tmp_name']);
            if ($imagedata == false) {
                $this->setError('Неверный формат');
                $logoerror = true;
            }
            if (!$logoerror) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newname = time() . '.' . $ext;

                @unlink(_ROOT . 'upload/' . $newname);
                move_uploaded_file($file['tmp_name'], _ROOT . 'upload/' . $newname);
                $this->_user->avatar = '/upload/' . $newname;
                $this->profileform->avatarprev->setUrl($this->_user->avatar);
            }
        }




        if (!$this->isError()) {


            $this->_user->username = $username;


            if ($password != '') {
                $this->_user->userpass = (\password_hash($password, PASSWORD_DEFAULT));
            }
            $this->_user->hashdata = "";
            $this->_user->city_id = $this->profileform->city->getValue();
            $this->_user->cityname = $this->profileform->city->getValueName();
            $this->_user->phone = $this->profileform->phone->getText();

            $this->_user->Save();
            System::setUser($this->_user);
            $this->setSuccess('Изменения сохранены');
        }
        $this->profileform->password->setText('');
        $this->profileform->confirm->setText('');
    }

}
