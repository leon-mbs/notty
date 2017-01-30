<?php

namespace App\Pages;

use Zippy\Html\Form\Form;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\File;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Label;
use Zippy\Html\Panel;
use App\System\System;
use App\System\Helper;
use App\Entity\User;
use App\Entity\VendorService;
use \Zippy\Html\DataList\DataView;
use \ZCL\DB\EntityDataSource;
use \Zippy\Html\Link\ClickLink;

class VendorProfile extends Base
{

    private $_editeds = 0;

    public function __construct() {
        parent::__construct();

        System::checkLogined(User::ROLE_VENDOR);
        $plist = $this->add(new Panel("plist"));

        $plist->add(new DataView("slist", new EntityDataSource("\\App\\Entity\\VendorService", ""), $this, 'OnServiceRow'))->Reload();

        $plist->add(new ClickLink("addnew", $this, "onAddNew"));

        $this->add(new Panel("pedit"))->setVisible(false);


        $sform = $this->pedit->add(new Form("sform"));

        $sform->add(new SubmitButton("save"))->onClick($this, 'onSave');
        $sform->add(new ClickLink("cancel", $this, "onCancel"));
        $sform->add(new DropDownChoice("etype", VendorService::getTypeList()))->onChange($this, "onChangeService");
        $sform->add(new DropDownChoice("esubtype"));
        $sform->add(new TextInput("ecity"));
        $sform->add(new TextInput("ecityid"));
        $sform->add(new TextInput("eaddress"));
        $sform->add(new TextInput("latitude"));
        $sform->add(new TextInput("longitude"));
        $sform->add(new TextInput("ecost"));
        $sform->add(new TextInput("ecostmin"));
        $sform->add(new TextInput("ecostdel"));
        $sform->add(new TextInput("ecapacity"));
        $sform->add(new CheckBox("ecatering"));
        $sform->add(new File("image"));
        $sform->add(new TextArea("edesc"));
        $sform->add(new \Zippy\Html\Image("imgprev"))->setVisible(false);;

        $egenre = $sform->add(new \Zippy\Html\Form\CheckBoxList("egenre", "<br>"));
        foreach (VendorService::getGenres() as $key => $value) {
            $egenre->AddCheckBox($key, false, $value);
        }


        $user = System::getUser();

        $this->add(new Panel("patext"));
        $this->patext->add(new Label("abouttext"))->setText($user->vendorresume, true);
        $this->patext->add(new ClickLink("editabout", $this, "onAEdit"));

        $this->add(new Panel("paedit"))->setVisible(false);
        ;
        $aeditform = $this->paedit->add(new Form("aeditform"));
        $aeditform->add(new TextArea("aboutmeedit"));
        $aeditform->add(new ClickLink("acancel", $this, "onACancel"));
        $aeditform->onSubmit($this, 'onASave');


        $this->_tvars['esubtype'] = false;
        $this->_tvars['ecost'] = false;
        $this->_tvars['efood'] = false;
        $this->_tvars['egenre'] = false;
        $this->_tvars['ecapacity'] = false;
        $this->_tvars['eimage'] = false;
        $this->_tvars['eaddress'] = false;
    }

    public function OnServiceRow($datarow) {
        $service = $datarow->getDataItem();

        $datarow->add(new Label("type", $service->typename));
        $datarow->add(new Label("subtype", $service->subtypename));
        $datarow->add(new Label("cost", number_format($service->cost / 100, 2, '.', '')));
        $datarow->add(new Label("desc", $service->desc));
        $datarow->add(new ClickLink("edits", $this, "onEdit"));
        $datarow->add(new ClickLink("dels", $this, "onDel"));
    }

    public function onAEdit($sender) {
        $user = System::getUser();

        $this->paedit->aeditform->aboutmeedit->setText($user->vendorresume);
        $this->patext->setVisible(false);
        $this->paedit->setVisible(true);
    }

    public function onACancel($sender) {

        $this->patext->setVisible(true);
        $this->paedit->setVisible(false);
    }

    public function onASave($sender) {
        $user = System::getUser();
        $user->vendorresume = $this->paedit->aeditform->aboutmeedit->getText();
        $user->save();
        $this->patext->abouttext->setText($user->vendorresume, true);
        $this->patext->setVisible(true);
        $this->paedit->setVisible(false);
    }

    public function onDel($sender) {
        $item = $sender->getOwner()->getDataItem();
        VendorService::delete($item->service_id);
        $this->plist->slist->Reload();
    }

    public function onAddNew($sender) {


        $this->pedit->sform->clean();
       // $this->pedit->sform->imgprev->setVisble(false);

        $this->plist->setVisible(false);
        $this->pedit->setVisible(true);
    }

    public function onCancel($sender) {
        $this->plist->setVisible(true);
        $this->pedit->setVisible(false);
    }

    public function onEdit($sender) {
        $service = $sender->getOwner()->getDataItem();
        $this->_editeds = $service->service_id;
        $form = $this->pedit->sform;
        $form->clean();

        $form->etype->setValue($service->servicetype);
        $this->onChangeService($form->etype);
        $form->esubtype->setValue($service->subtype);
        $form->ecityid->setText($service->city_id);
        $form->latitude->setText($service->latitude);
        $form->longitude->setText($service->longitude);
        $form->eaddress->setText($service->address);
        $form->ecity->setText($service->city);

        $form->ecapacity->setText($service->capacity);
        $form->ecost->setText(Helper::fm($service->cost));
        $form->ecostmin->setText(Helper::fm($service->costmin));
        $form->ecostdel->setText(Helper::fm($service->costdel));
        $form->ecatering->setChecked($service->catering);

        if ($service->image > 0) {
            $form->imgprev->setVisible(true);
            $form->imgprev->setUrl('/images/' . $service->image);
        } else {
            $form->imgprev->setVisible(false);
        }


        foreach ($service->genre as $id) {
            $form->egenre->setChecked($id, true);
        }

        $this->plist->setVisible(false);
        $this->pedit->setVisible(true);
    }

    public function onSave($form) {

        $service = new VendorService();
        if ($this->_editeds > 0) {
            $service = VendorService::load($this->_editeds);
        }

        $form = $this->pedit->sform;

        $service->servicetype = $form->etype->getValue();
        if ($service->servicetype == 0) {
            $this->setError('Select service!');
            return;
        }


        $service->subtype = $form->esubtype->getValue();
        $service->typename = $form->etype->getValueName();
        $service->subtypename = $form->esubtype->getValueName();


        $service->city = $form->ecity->getText();
        $service->city_id = $form->ecityid->getText();
        $service->longitude = $form->longitude->getText();
        $service->latitude = $form->latitude->getText();
        $service->address = $form->eaddress->getText();
        if (strlen($service->city_id) == 0) {
            $this->setError('Select addreres known by map service!');
            return;
        } $service->desc = $form->edesc->getText();
        $service->capacity = $form->ecapacity->getText();
        if ($service->capacity == '')
            $service->capacity = 0;
        $service->cost = $form->ecost->getText();
        if ($service->cost == '')
            $service->cost = 0;
        $service->cost = $service->cost * 100;
        $service->costmin = $form->ecostmin->getText();
        if ($service->costmin == '')
            $service->costmin = 0;
        $service->costmin = $service->costmin * 100;
        $service->costdel = $form->ecostdel->getText();
        if ($service->costdel == '')
            $service->costdel = 0;
        $service->costdel = $service->costdel * 100;
        $service->catering = $form->ecatering->isChecked();
        $service->genre = $form->egenre->getCheckedList();

        $file = $form->image->getFile();
        if (strlen($file['tmp_name']) > 0) {
            $imagedata = @getimagesize($file['tmp_name']);
            if ($imagedata == false) {
                $this->setError('Invalid image');
            } if ($file['size'] > 1000000) {
                $this->setError('File is > 1Mb');
            } else {
                $image = new \App\Entity\Image();
                $image->content = file_get_contents($file['tmp_name']);
                $image->mime = $imagedata['mime'];
                $image->save();
                if ($service->image > 0) {
                    \App\Entity\Image::delete($service->image); //previous
                }
                $service->image = $image->image_id;
            }
        }
        if ($this->isError()) {
            return;
        }
        $service->save();
        $this->plist->slist->Reload();

        $this->plist->setVisible(true);
        $this->pedit->setVisible(false);
    }

    public function onChangeService($sender) {

        $form = $this->pedit->sform;
        $type = $sender->getValue();
        $this->_tvars['esubtype'] = false;
        $this->_tvars['ecost'] = true;
        $this->_tvars['ecapacity'] = false;
        $this->_tvars['efood'] = false;
        $this->_tvars['eimage'] = false;
        $this->_tvars['egenre'] = false;
        $this->_tvars['eaddress'] = false;

        $subtypelist = array();
        if ($type == VendorService::SERVICE_VENUE) {
            $subtypelist = VendorService::getVenueTypes();
            $this->_tvars['ecost'] = true;
            $this->_tvars['ecapacity'] = true;
            $this->_tvars['eimage'] = true;
            $this->_tvars['eaddress'] = true;
        }
        if ($type == VendorService::SERVICE_MUSIC) {
            $subtypelist = VendorService::getMusicTypes();
            $this->_tvars['egenre'] = true;
            $this->_tvars['ecost'] = true;
        }
        if ($type == VendorService::SERVICE_STAFF) {
            $subtypelist = VendorService::getStaffTypes();
            $this->_tvars['ecost'] = true;
        }
        if ($type == VendorService::SERVICE_TRAN) {
            $subtypelist = VendorService::getTransportTypes();
            $this->_tvars['ecost'] = true;
            $this->_tvars['ecapacity'] = true;
            $this->_tvars['eimage'] = true;
        }
        if ($type == VendorService::SERVICE_FOOD) {

            $this->_tvars['ecost'] = false;
            $this->_tvars['efood'] = true;
        }

        if (count($subtypelist) > 0) {
            $this->_tvars['esubtype'] = true;
            $form->esubtype->setOptionList($subtypelist);
        }
    }

}
