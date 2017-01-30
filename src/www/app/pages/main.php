<?php

namespace App\Pages;

use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Image;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Link\RedirectLink;
use \Zippy\Html\Link\ClickLink;
use \ZCL\DB\EntityDataSource;
use \App\System\Application as App;
use \App\System\System;
use \App\System\Helper;
use \App\System\Filter;

/**
 * Главная страница
 */
class Main extends Base
{

    public function __construct() {
        parent::__construct();
        System::checkLogined();
    }

}
