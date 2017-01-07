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

    protected function init()
    {
        $this->topic_id = 0;
    }

    public static function findByNode($node_id)
    {
        return self::find("topic_id in (select topic_id from topicnode where node_id={$node_id})");
    }

    public static function deleteByNode($node_id)
    {

        $conn = \ZCL\DB\DB::getConnect();

        $conn->Execute("delete from topicnode where node_id_id= {$node_id} ");

        $conn->Execute("delete from topic where topic_id not  in (select topic_id from topicnode)");
    }

    public function addToNode($node_id)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("insert into topicnode(topic_id,node_id)values({$this->topic_id},{$node_id})");
    }

    public function removeFromNode($node_id)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete from topicnode where topic_id= {$this->topic_id} and node_id = {$node_id}");
    }

    protected function beforeDelete()
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete from topicnode where topic_id=" . $this->topic_id);

        return true;
    }

    public function saveTags($tags)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete from tags where topic_id=" . $this->topic_id);

        foreach ($tags as $tag) {
            $conn->Execute("insert tags (topic_id,tagvalue) values (" . $this->topic_id . "," . $conn->qstr($tag) . ")");
        }
    }

    public function getTags()
    {
        $conn = \ZCL\DB\DB::getConnect();
        return $conn->GetCol("select distinct tagvalue from tags where topic_id=" . $this->topic_id);
    }

    public function getSuggestionTags()
    {
        $conn = \ZCL\DB\DB::getConnect();
        return $conn->GetCol("select distinct tagvalue from tags where topic_id <> " . $this->topic_id);
    }

}
