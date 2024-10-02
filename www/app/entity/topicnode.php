<?php

namespace App\Entity;

/**
 *  Класс  инкапсулирующий  комбинацию  топик-узел (используется  для поиска)
 * @table=topicnodeview
 * @view=topicnodeview
 * @keyfield=tn_id
 */
class TopicNode extends \ZCL\DB\Entity
{

    protected function init() {
        $this->tn_id = 0;
    }

    /**
     * поиск по  тексту
     * 
     * @param mixed $text
     */
    public static function searchByText($text, $type, $title) {
        global $logger;
        $user_id=\App\System::getUser()->user_id;
   
        $arr = array();
        $text = trim($text);

        if ($type == 1) {
            $arr[] = Topic::qstr('%' . $text . '%');
        } else {
            $ta = explode(' ', $text);
            foreach ($ta as $a) {
                $arr[] = Topic::qstr('%' . $a . '%');
            }
        }


        $where =  "  (ispublict=1 or tuser_id={$user_id} ) ";

        foreach ($arr as $t) {


            if ($title == false) {
                $where .= "and ( title like {$t}  or content like {$t} )";
            } else {
                $where .= " and  title like {$t} ";
            }
        }

 

        $list = TopicNode::find($where);

        return $list;
    }

    /**
     * поиск по  тегу
     * 
     * @param mixed $tag
     */
    public static function searchByTag($tag) {
        $user_id=\App\System::getUser()->user_id;
   
        $where = "   (ispublict=1 or tuser_id={$user_id} ) and  topic_id in (select topic_id from tags where tagvalue  = " . Topic::qstr($tag) . " )  " ;

        $list = TopicNode::find($where);

        return $list;
    }

    // поиск избранных 
    public static function searchFav() {

        $user_id=\App\System::getUser()->user_id;
        
        $where = "    topic_id in (select topic_id from fav where   user_id= {$user_id} )";

        $list = TopicNode::find($where);

        return $list;
    }



}
