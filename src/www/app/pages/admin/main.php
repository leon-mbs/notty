<?php

namespace App\Pages\Admin;

use \App\Entity\User;
use \App\System\System;

class Main extends \App\Pages\Base
{

    public function __construct() {
        parent::__construct();
        System::checkLogined(User::ROLE_ADMIN);
    }

}
