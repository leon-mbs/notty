<?php

namespace App\Entity;

use \ZCL\DB\Entity;

/**
 *  Класс  инкапсулирующий   сущность  файл  
 * @table=files
 * @keyfield=file_id
 */
class File extends Entity
{

    protected function init() {
        
    }

    public static function findByTopic($topic_id) {
        return File::findBySql("select file_id,topic_id,details from files where  topic_id=" . $topic_id);
    }

    protected function beforeSave() {
        parent::beforeSave();

        $this->details = "<detail>";
        $this->details .= "<filename>{$this->filename}</filename>";
        $this->details .= "<mime>{$this->mime}</mime>";
        $this->details .= "<size>{$this->size}</size>";

        $this->details .= "</detail>";

        return true;
    }

    protected function afterLoad() {



        if (strlen($this->details) > 0) {

            $xml = simplexml_load_string($this->details);
            $this->filename = (string) ($xml->filename[0]);

            $this->mime = (string) ($xml->mime[0]);
            $this->size = (int) ($xml->size[0]);
        }
        parent::afterLoad();
    }

}
