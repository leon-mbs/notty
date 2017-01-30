<?php

namespace App\Entity;

use \ZCL\DB\Entity;

/**
 *  Класс  инкапсулирующий   сущность  коментария
 * @table=blog_comments
 * @keyfield=comment_id
 */
class Comment extends Entity
{

    protected function init() {
        $this->comment_id = 0;
        $this->createdon = time();
    }

    protected function afterLoad() {

        $this->createdon = strtotime($this->createdon);
    }

}
