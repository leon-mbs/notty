<?php

namespace App\Pages;

use \App\Application as App;
use \App\System;
use \App\Helper;
use \App\Entity\User;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Link\RedirectLink;
use \Zippy\Html\Panel;
use \Zippy\Html\Label;

class Base extends \Zippy\Html\WebPage
{

    

    public function __construct() {
        parent::__construct();

        global $_config;

 
        $this->add(new ClickLink("logout", $this, "OnExit"));

        $user = System::getUser();

     
        if ($user->user_id == 0) {
           App::Redirect("\\App\\Pages\\UserLogin");
           return;
        }
   
      }

    public function OnExit($sender) {

        setcookie("remember", '', 0, '/');
        System::setUser(new \App\Entity\User());

        App::Redirect("\\App\\Pages\\UserLogin");
    }

    public function setError($msg ) {
        System::setErrorMsg($msg);
    }
   

    public function setSuccess($msg ) {
        System::setSuccessMsg($msg);
    }

    public function setWarn($msg ) {
           System::setWarnMsg($msg);
    }

    public function setInfo($msg ) {
        System::setInfoMsg($msg);
    }

    final protected function isError() {
        return (strlen(System::getErrorMsg()) > 0 || strlen(System::getErrorMsg()) > 0);
    }
    
    
    protected function afterRender() {

        $user = System::getUser();
        if (strlen(System::getErrorMsg() ?? '') > 0) {

            $this->addJavaScript("toastr.error('" . System::getErrorMsg() . "','',{'timeOut':'8000'})        ", true);
        }

        if (strlen(System::getWarnMsg() ?? '') > 0) {
            $this->addJavaScript("toastr.warning('" . System::getWarnMsg() . "','',{'timeOut':'4000'})        ", true);
        }
        if (strlen(System::getSuccesMsg() ?? '') > 0) {
            $this->addJavaScript("toastr.success('" . System::getSuccesMsg() . "','',{'timeOut':'2000'})        ", true);
        }
        if (strlen(System::getInfoMsg() ?? '') > 0) {
            $this->addJavaScript("toastr.info('" . System::getInfoMsg() . "','',{'timeOut':'3000'})        ", true);
        }

        $this->setError('');
        $this->setSuccess('');
        $this->setInfo('');
        $this->setWarn('');
        
        parent::afterRender()  ;
    }

     
}
