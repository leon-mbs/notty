<?php

namespace App\Pages;

use \App\Entity\User;
use \App\System;
use \App\Helper;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Panel;
use \Zippy\Html\Label;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\DataList\Paginator;
use \ZCL\DB\EntityDataSource;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Link\RedirectLink;

class Users extends \App\Pages\Base
{

    private $_ds;
    private $_user ;

    public function __construct() {
        parent::__construct();

        if (System::getUser()->username != 'admin') {
            Application::RedirectURI("/");
            return;
        }

        $plist = $this->add(new Panel('plist'));
        $plist->add(new ClickLink('adduser',$this,'OnAddNew')) ;
        

        $this->_ds = new EntityDataSource("\\App\\Entity\\User", "username <>  'admin'", "username asc");
        $plist->add(new DataView("userrow", $this->_ds, $this, 'OnRow'));
      
        $plist->userrow->Reload();

        $pedit = $this->add(new Panel('pedit'));
        $pedit->setVisible(false);
        
        $pedit->add(new  Form('editform'))->onSubmit($this,'onSave');
        $pedit->editform->add(new TextInput('editname')) ;
        $pedit->editform->add(new TextInput('editlogin')) ;
        $pedit->editform->add(new TextInput('editpass')) ;
        $pedit->editform->add(new TextInput('editconfirm')) ;
        $pedit->editform->add(new CheckBox('editdisabled')) ;
        
        
        $pedit->add(new ClickLink('cancel',$this,'OnCancel')) ;
    }

    //удаление  юзера
    public function OnRemove($sender) {
        $user = $sender->getOwner()->getDataItem();
        User::delete($user->user_id);
        $this->plist->userrow->Reload();
    }

    public function OnRow(\Zippy\Html\DataList\DataRow $datarow) {
        $item = $datarow->getDataItem();
        $datarow->add(new  Label("userlogin",$item->userlogin));
        $datarow->add(new  Label("username",$item->username));
        $datarow->add(new  ClickLink("edit", $this, 'OnEdit'))    ;
        $datarow->add(new  ClickLink("del", $this, 'OnRemove'))    ;
        $datarow->setAttribute('style', $item->disabled == 1 ? 'color: #aaa' : null);
      }
  
    public function OnAddNew($sender) {
        $this->_user = new User();  
        $this->plist->setVisible(false);
        $this->pedit->setVisible(true);
        $this->pedit->editform->clean();
    }   
    
    public function OnCancel($sender) {
          
        $this->plist->setVisible(true);
        $this->pedit->setVisible(false);
        $this->pedit->editform->clean(); 
    }   
    
    public function OnEdit($sender) {
        $this->_user = $sender->getOwner()->getDataItem();

        $this->plist->setVisible(false);
        $this->pedit->setVisible(true);
        $this->pedit->editform->clean();        
        $this->pedit->editform->editname->setText($this->_user->username);
        $this->pedit->editform->editlogin->setText($this->_user->userlogin);
        $this->pedit->editform->editdisabled->setChecked($this->_user->disabled==1);
    
        
        
    }
    
    public function onSave($sender) {
        
        $this->_user->userlogin = $sender->editlogin->getText();
        $this->_user->username = $sender->editname->getText();
        $this->_user->disabled = $sender->editdisabled->isChecked()?1:0 ;
       
        $user = User::getByLogin($this->_user->userlogin);
        if ($user instanceof User) {
            if ($user->user_id != $this->_user->user_id) {
                $this->setError('Неуникальный логин');
                return;
            }
        }        
        
        $pass=$sender->editpass->getText();
        $confirm=$sender->editconfirm->getText();
        if(strlen($pass)==0 && $this->_user->user_id==0) {
            $this->setError('Не задан  пароль');
            return;
        }
        
        if(strlen($pass)>0 ) {
            if(strlen($confirm)==0 ) {
               $this->setError('Невкрное подтверждение ');
               return;
            }
            if($confirm != $pass ) {
               $this->setError('Невкрное подтверждение ');
               return;
            }
            $this->_user->userpass=  (\password_hash($pass, PASSWORD_DEFAULT));;
        }       
        
         
 
        
        if($this->_user->username=='admin') {
            $this->setError('Недопустимое имя');
            return;
        }
        $this->_user->save();
        $this->plist->userrow->Reload();
        
        $this->plist->setVisible(true);
        $this->pedit->setVisible(false);
        $this->pedit->editform->clean(); 
    }   

}
