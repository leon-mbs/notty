<?php

namespace App\Pages;

use \App\Entity\User;
use \App\System;
use \App\Helper;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
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
        $plist->add(new DataView("userrow", $this->_ds, $this, 'OnAddUserRow'));
      
        $plist->userrow->Reload();

        $pedit = $this->add(new Panel('pedit'));
        $pedit->setVisible(false);
        
        $pedit->add(new  Form('editform'))->onSubmit($this,'onSave');
        $pedit->editform->add(new TextInput('editname')) ;
        $pedit->editform->add(new TextInput('editpass')) ;
        $pedit->editform->add(new TextInput('editemail')) ;
        
        $pedit->add(new ClickLink('cancel',$this,'OnCancel')) ;
    }

    //удаление  юзера
    public function OnRemove($sender) {
        $user = $sender->getOwner()->getDataItem();
        User::delete($user->user_id);
        $this->plist->userrow->Reload();
    }

    public function OnAddUserRow(\Zippy\Html\DataList\DataRow $datarow) {
        $item = $datarow->getDataItem();
        $datarow->add(new  ClickLink("username", $this, 'OnEdit'))->setValue($item->username);
        $datarow->add(new  ClickLink("del", $this, 'OnRemove'))    ;
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
        $this->pedit->editform->editname->setText($this->_user->username);
        $this->pedit->editform->editpass->setText('');
        $this->pedit->editform->editemail->setText($this->_user->email);
        
    }
    
    public function onSave($sender) {
        $pass=$sender->editpass->getText();
        if(strlen($pass)==0 && $this->_user->user_id==0) {
            $this->setError('Не задан  пароль');
            return;
        }
        $this->_user->username = $sender->editname->getText();
        $this->_user->email = $sender->editemail->getText();
        if(strlen($pass)>0 ) {
            $this->_user->userpass= $pass;
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
