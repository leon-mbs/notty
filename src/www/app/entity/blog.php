<?php

namespace App\Entity;

use \ZCL\DB\Entity;

/**
 *  Класс  инкапсулирующий   сущность  Blog
 * @table=blog
 * @keyfield=blog_id
 */
class Blog extends Entity
{

    protected function init() {
        $this->blog_id = 0;
        $this->createdon = time();
    }

    protected function afterLoad() {

        $this->createdon = strtotime($this->createdon);
    }

    protected function beforeDelete() {

        Image::delete($this->titleimage);
    }

}
