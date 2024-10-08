<?php

namespace App\Entity;

use \ZCL\DB\TreeEntity;

/**
 *  Класс  инкапсулирующий   узел дерева
 * @table=nodes
 * @view=nodesview
 * @keyfield=node_id
 * @parentfield=pid
 * @pathfield=mpath
 */
class Node extends TreeEntity
{
 
    protected function init() {
        $this->ispublic = 1;
        $this->node_id = 0;
        $this->pid = 0;
        $this->mpath = '';
    }

    protected function beforeSave() {
        parent::beforeSave();

        $this->detail = "<detail>";
   //     $this->detail .= "<ispublic>{$this->ispublic}</ispublic>";
      //  $this->detail .= "<access><![CDATA[{$this->detail}]]></access>";
        $this->detail .= "</detail>";

        return true;
    }

    protected function afterLoad() {
        
        $xml = @simplexml_load_string($this->detail);
    //    $this->ispublic = (int)($xml->ispublic[0] ??0);

        parent::afterLoad();
    } 
    protected function beforeDelete() {
        $conn = \ZCL\DB\DB::getConnect();
        $tlist= Topic::find("topic_id in (select topic_id from topicnode where node_id=" . $this->node_id. " and islink != 1  )" );
        foreach($tlist as $t){
            Topic::delete($t->topic_id);
        }
        
        $conn->Execute("delete from topicnode where node_id=" . $this->node_id);

        return '';
    }

    /**
     * получение  родительских узлов
     * 
     */
    public function getParents() {

        // $ids = str_split ($this->mpath,8);   ;

        $list = array();

        $parent = $this;
        do {
            $list[] = $parent->title;
            $parent = Node::load($parent->pid);
        } while ($parent instanceof Node);

        return $list;
    }

    /**
     * цепочка  названий узлов до  корня
     * 
     */
    public static function nodes($node_id) {

        $node = Node::load($node_id);
        $list = $node->getParents();
        $list = array_reverse($list);

        $path = implode(" > ", $list);
        return $path;
    }    
    
}
