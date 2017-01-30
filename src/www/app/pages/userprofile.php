<?php

namespace App\Pages;

use Zippy\Binding\PropertyBinding as Bind;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Label;
use App\System\System;
use App\Entity\User;
use App\Entity\Image;

class UserProfile extends Base
{

    public function __construct() {
        parent::__construct();

        $user = System::getUser();

        if ($user->userrole == 0) {
            $this->setWarn("You  have to select a role!");
        }



        $accform = new \Zippy\Html\Form\Form('accform');
        $accform->add(new TextInput('email', $user->email));
        $accform->add(new TextInput('username', $user->username));
        $accform->add(new DropDownChoice('role', array(User::ROLE_ORGANIZER => 'Organizer', User::ROLE_VENDOR => 'Vendor', User::ROLE_CLUBBER => 'Clubber')))->setValue($user->userrole);


        $accform->onSubmit($this, 'onsubmit');
        $this->add($accform);


        $this->add(new \Zippy\Html\Image("avatarprev", $user->avatar));
        $avform = new \Zippy\Html\Form\Form('avform');
        $avform->add(new \Zippy\Html\Form\File('avatar'));
        $avform->onSubmit($this, 'onavsubmit');
        $this->add($avform);

        $passform = new \Zippy\Html\Form\Form('passform');
        $passform->add(new TextInput('userpassword'));
        $passform->add(new TextInput('confirmpassword'));
        $passform->onSubmit($this, 'onsubmitpass');
        $this->add($passform);
    }

    //password
    public function onsubmitpass($sender) {
        $this->setError('');

        if ($sender->userpassword->getText() == '') {
            $this->setError('Enter password');
        } else
        if ($sender->confirmpassword->getText() == '') {
            $this->setError('Enter confirmation');
        } else
        if ($sender->userpassword->getText() != $sender->confirmpassword->getText()) {
            $this->setError('Invalid confirmation');
        }


        if (!$this->isError()) {
            $user = System::getUser();
            $user->userpass = (\password_hash($sender->userpassword->getText(), PASSWORD_DEFAULT));

            $user->save();
            $this->setSuccess("Saved succesfull");
        }
        $sender->userpassword->setText('');
        $sender->confirmpassword->setText('');
    }

    //avatar
    public function onavsubmit($sender) {
        $this->setError('');

        $file = $sender->avatar->getFile();
        if (strlen($file['tmp_name']) > 0) {


            $imagedata = @getimagesize($file['tmp_name']);
            if ($imagedata == false) {
                $this->setError('Invalid image');
                $logoerror = true;
            }
            if ($imagedata[0] != $imagedata[1]) {
                // $this->setError('РР·РѕР±СЂР°Р¶РµРЅРёРµ РґРѕР»Р¶РЅРѕ Р±С‹С‚СЊ  РєРІР°РґСЂР°С‚РЅС‹Рј');
                //  $logoerror = true;
            }
            if (!$logoerror) {
                // $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                // $newname = time() . '.' . $ext;
                //@unlink(_ROOT . 'upload/' . $newname);
                //move_uploaded_file($file['tmp_name'], _ROOT . 'upload/' . $newname);

                $image = new Image();
                $image->content = file_get_contents($file['tmp_name']);
                $image->mime = $imagedata['mime'];
                $image->save();

                $user = System::getUser();

                $user->avatar = '/images/' . $image->image_id;
                ;
                $user->save();
                $this->avatarprev->setUrl($user->avatar);
                $this->setSuccess("Saved succesfully");
            }
        }
    }

    //profile
    public function onsubmit($sender) {
        $this->setError('');

        if (!$this->isError()) {
            $user = System::getUser();
            $user->email = $sender->email->getText();
            $user->username = $sender->username->getText();

            if ($user->userrole != User::ROLE_ADMIN) {


                $user->userrole = $sender->role->getValue();
            }
            if ($user->userrole == 0) {
                $this->setError('Select role!');
                return;
            }

            //$uploaddir = UPLOAD_USERS;
            // @mkdir($uploaddir) ;

            $user->save();
            $this->setSuccess("Saved succesfull");
        }
    }

}
