<?php

namespace App\Pages;

class Home extends \App\Pages\Base
{

    public function __construct() {
        parent::__construct();
        
        $this->_tvars['landing'] = true;
    }

}
