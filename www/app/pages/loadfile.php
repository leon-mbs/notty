<?php

namespace App\Pages;

class LoadFile extends Base
{

    public function __construct($file_id) {
        parent::__construct();

        $file = \App\Entity\File::load($file_id);

        if (strlen($file->mime) > 0) {  //картинка
            header("Content-Type: " . $image->mime);
        } else {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $file->filename);
            header('Content-Transfer-Encoding: binary');
        }

        header("Content-Length: " . strlen($file->content));

        echo $file->content;
        exit;
    }

}
