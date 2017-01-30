<?php

namespace App\Pages;

class LoadImage extends Base
{

    public function __construct($image_id) {
        parent::__construct();

        $image = \App\Entity\Image::load($image_id);

        header("Content-Type: " . $image->mime);
        header("Content-Length: " . strlen($image->content));

        echo $image->content;
        exit;
    }

}
