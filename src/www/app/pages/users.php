<?php

namespace App\Pages ;

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

    public function __construct() {
        parent::__construct();

        if(System::getUser()->username != 'admin'){
           Application::toPage("/"); 
           return;
        }

        $plist = $this->add(new Panel('plist'));
        $plist->add(new Form('filterform'))->onSubmit($this, 'filterOnSubmit');
        $plist->filterform->add(new TextInput('search'));
        

        $this->_ds = new EntityDataSource("\\App\\Entity\\User", "username <>  'admin'", "username asc");
        $plist->add(new DataView("userrow", $this->_ds, $this, 'OnAddUserRow'));
        $plist->userrow->setPageSize(50);
        $plist->add(new Paginator("paginator", $plist->userrow));
        $plist->userrow->Reload();

        $pedit = $this->add(new Panel('pedit'));
        $pedit->setVisible(false);
    }

    //удаление  юзера
    public function OnRemove($sender) {
        $user = $sender->getOwner()->getDataItem();
        User::delete($user->user_id);
        $this->plist->userrow->Reload();
    }

    public function OnAddUserRow(\Zippy\Html\DataList\DataRow $datarow) {
        $item = $datarow->getDataItem();
        $datarow->add(new \Zippy\Html\Link\ClickLink("username", $this, 'OnEdit'))->setValue($item->username);
        $datarow->add(new \Zippy\Html\Label("created", date('d.m.Y', $item->createdon)));
        
    }

    public function filterOnSubmit($sender) {
        $where = "userrole <> " . User::ROLE_ADMIN;
        $search = $this->plist->filterform->search->getText();
        if (strlen($search) > 0) {
            $search = User::qstr('%' . $search . '%');
            $where .= "  and  username like {$search}  ";
        }
 
        $this->_ds->setWhere($where);
        $this->plist->userrow->Reload();
    }

    public function OnEdit($sender) {
        $user = $sender->getOwner()->getDataItem();

        $this->plist->setVisible(false);
        $this->pedit->setVisible(true);
    }

}
