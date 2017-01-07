<?php

namespace App\Pages;

use \App\Application as App;
use \App\Helper;
use \App\System;
use \Zippy\Html\Label;

class ShowTopic extends \Zippy\Html\WebPage
{

    public function __construct($topic)
    {
        $this->add(new Label("content", $topic, true));
    }

}
