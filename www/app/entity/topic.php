<?php

namespace App\Entity;

/**
 *  Класс  инкапсулирующий топик
 * @table=topics
 * @view=topicsview
 * @keyfield=topic_id
 */
class Topic extends \ZCL\DB\Entity
{

     public const PRIVATE       = 0;   
     public const PUBLIC        = 1;   
     public const OUTER         = 2;   
 
     protected function init() {
        $this->topic_id = 0;
        $this->acctype = 0;
        $this->updatedon=time()  ;
    }


    protected function beforeSave() {
        parent::beforeSave();

        $this->detail = "<detail>";
        $this->detail .= "<updatedon>{$this->updatedon}</updatedon>";
        $this->detail .= "</detail>";

        return true;
    }

    protected function afterLoad() {
        //для совместимости
        if(strpos($this->content,'<detail><![CDATA[')!==false) {
          $xml = @simplexml_load_string($this->content);
          $this->content = (string)($xml->detail[0]);
            
        }
        
        $xml = @simplexml_load_string($this->detail);
        $this->updatedon = (int)($xml->updatedon[0]);

        parent::afterLoad();
    }    
    
    /**
     * список топиков  для  узла
     * 
     * @param mixed $node_id
     */
    public static function findByNode($node_id) {
        $user_id=\App\System::getUser()->user_id;
   
        return self::find(" (acctype=0 or user_id={$user_id} ) and topic_id in (select topic_id from topicnode where node_id={$node_id})");
    }

    /**
     * удаление  топиков узла
     * 
     * @param mixed $node_id
     */
    public static function deleteByNode($node_id) {

        $conn = \ZCL\DB\DB::getConnect();

        $conn->Execute("delete from topicnode where node_id= {$node_id} ");

        $conn->Execute("delete from topics where topic_id not  in (select topic_id from topicnode)");
        $conn->Execute("delete from files where topic_id not  in (select topic_id from topicnode)");
    }

    /**
     * добавить  к  узлу
     * 
     * @param mixed $node_id
     */
    public function addToNode($node_id,$islink=false) {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete from topicnode where topic_id={$this->topic_id} and node_id = {$node_id} ");
        $conn->Execute("insert into topicnode(topic_id,node_id,islink)values({$this->topic_id},{$node_id}," . ($islink ? 1:0  ). ")");
    }

    /**
     * удалить с  узла
     * 
     * @param mixed $node_id
     */
    public function removeFromNode($node_id) {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete from topicnode where topic_id= {$this->topic_id} and node_id = {$node_id}");
    }

    /**
     * @see Entity
     * 
     */
    protected function beforeDelete() {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete from files where topic_id=" . $this->topic_id);
        $conn->Execute("delete from topicnode where topic_id=" . $this->topic_id);

        return "";
    }

    /**
     * записать тэги
     * 
     * @param mixed $tags
     */
    public function saveTags($tags) {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete from tags where topic_id=" . $this->topic_id);

        foreach ($tags as $tag) {
            $tag = trim($tag);
            if(strlen($tag)==0) continue;
            
            $conn->Execute("insert tags (topic_id,tagvalue) values (" . $this->topic_id . "," . $conn->qstr($tag) . ")");
        }
    }

    /**
     * получить теги
     * 
     */
    public function getTags() {
        $tl = array();
        $conn = \ZCL\DB\DB::getConnect();
        $rc = $conn->GetCol("select distinct tagvalue from tags where topic_id=" . $this->topic_id);
        foreach($rc as $k=>$v){
           if(strlen($v)>0)$tl[$k] = $v;
        }
        return $tl;
    }

    /**
     * получить  подсказки из существующих тегов
     * 
     */
    public function getSuggestionTags() {
        $conn = \ZCL\DB\DB::getConnect();
        return $conn->GetCol("select distinct tagvalue from tags where topic_id <> " . $this->topic_id);
    }

   
    
    
    
}
