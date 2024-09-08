<?php

namespace App\Pages;

use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Panel;
use \Zippy\Html\Label;
use \Zippy\Html\Image;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Link\RedirectLink;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Link\BookmarkableLink;
use \Zippy\Html\Link\SubmitLink;
use \ZCL\DB\EntityDataSource;
use \App\Application as App;
use \App\System;
use \App\Helper;
use \App\Filter;
use \ZCL\BT\Tags;
use \ZCL\BT\Tree;
use \ZCL\BT\TreeNode;
use \App\Entity\Node;
use \App\Entity\Topic;
use \App\Entity\TopicNode;

/**
 * Главная страница
 */
class Main extends Base
{

    private $_edited = 0;
    private $clipboard = array();
    public $_tarr= array();
    public $_sarr= array();
    public $_farr = array();

    public function __construct() {
        parent::__construct();

        //дерево
        $tree = $this->add(new Tree("tree"));
        $tree->onSelectNode($this, "onTree");

        $this->ReloadTree();

        // редактирование  узла
        $this->add(new Form("nodeform"))->onSubmit($this, "OnNodeTitle");
        $this->nodeform->add(new TextInput("editnodetitle"));
        $this->nodeform->add(new TextInput("opname"));

        //тулбар дерева
        $this->add(new Button("treeadd"));
        $this->add(new ClickLink("treeedit"));
        $this->add(new ClickLink("treecut", $this, 'onNodeCut'));
        $this->add(new ClickLink("treepaste", $this, 'onNodePaste'));
        $this->add(new ClickLink("treedelete", $this, 'onNodeDelete'));


        //тулбар топиков
        $this->add(new ClickLink("topicadd", $this, 'onTopicAdd'));
        $this->add(new ClickLink("topicedit", $this, 'onTopicEdit'));
        $this->add(new ClickLink("topiccut", $this, 'onTopicCut'));
        $this->add(new ClickLink("topiccopy", $this, 'onTopicCopy'));
        $this->add(new ClickLink("topictag", $this, 'onTopicTag'));
        $this->add(new ClickLink("topicpaste", $this, 'onTopicPaste'));
        $this->add(new ClickLink("topicdelete", $this, 'onTopicDelete'));
        $this->add(new BookmarkableLink("topiclink"));

        //список  топиков
        $topiclist = $this->add(new \Zippy\Html\DataList\DataView('topiclist', new \Zippy\Html\DataList\ArrayDataSource($this, '_tarr'), $this, "onRow"));
        $topiclist->setCellClickEvent($this, 'onTopic');
        $topiclist->setSelectedClass('table-success');
        $topiclist->setPageSize(25);
        $this->add(new \Zippy\Html\DataList\Pager("pag", $topiclist));

        //содержимое топика
        $this->add(new Label("content"));

        //редактирование  топика
        $this->add(new Form("editform"));
        $this->editform->add(new TextInput("edittitle"));
        $this->editform->add(new \ZCL\BT\Tags("edittags"));
        $this->editform->add(new TextArea("editcontent"));
        $this->editform->add(new ClickLink("editcancel", $this, "onTopicCancel"));
        $this->editform->add(new SubmitLink("editsave"))->onClick($this, "onTopicSave");
        $this->editform->add(new CheckBox("editispublic"));
 

        //аплоад файла
        $this->add(new Form("fileform"))->onSubmit($this, "OnFile");
        $this->fileform->add(new \Zippy\Html\Form\File("editfile"));
        $this->add(new \Zippy\Html\DataList\DataView('filelist', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\ArrayPropertyBinding($this, '_farr')), $this, "onFileRow"));

        //форма поиска
        $this->add(new Form("sform"))->onSubmit($this, "OnSearch");
        $this->sform->add(new TextInput("skeyword"));
        $this->sform->add(new ClickLink("searchfav", $this, 'onSearchFav'));
        $this->sform->add(new CheckBox("searchtitle"));
        $this->sform->add(new DropDownChoice("searchtype"));

        //список  результата поиска
        $searchlist = $this->add(new \Zippy\Html\DataList\DataView('searchlist', new \Zippy\Html\DataList\ArrayDataSource($this, '_sarr'), $this, "onSearchRow"));
        $searchlist->setCellClickEvent($this, 'onSearchTopic');
        $searchlist->setSelectedClass('table-success');
        $searchlist->setSelectedClass('table-success');

        $this->add(new Panel("tpanel"));
        $this->tpanel->add(new \Zippy\Html\Link\LinkList("taglist"))->onClick($this, 'OnTagList');
        $this->tpanel->add(new ClickLink("setfav"))->onClick($this, 'onFav');
        $this->tpanel->add(new Label("addfile"));

        $this->_tvars['editor'] = false;
    }

    //добавить топик
    public function onTopicAdd($sender) {

        $this->_edited = 0;
        $this->editform->edittitle->setText('');
        $this->editform->editcontent->setText('');
        $this->editform->edittags->setTags(array());
        $topic = new Topic();
        $this->editform->edittags->setSuggestions($topic->getSuggestionTags());
        $this->_tvars['editor'] = true;
        \App\Session::getSession()->topic_id = $topic->topic_id;
    }

    //редактировать  топик
    public function onTopicEdit($sender) {
        $topic = Topic::load($this->topiclist->getSelectedRow()->getDataItem()->topic_id);
        
        $this->_edited = $topic->topic_id;
        \App\Session::getSession()->topic_id = $topic->topic_id;
        
        $this->editform->edittitle->setText($topic->title);
        $this->editform->edittags->setTags($topic->getTags());
        $this->editform->edittags->setSuggestions($topic->getSuggestionTags());
        $this->editform->editcontent->setText($topic->content);
        $this->editform->editispublic->setChecked($topic->ispublic);

        $this->_tvars['editor'] = true;
    }

    //сохраниение топика
    public function onTopicSave($sender) {

        $topic = $this->_edited > 0 ? Topic::load($this->_edited) : new Topic();
        $topic->title = $this->editform->edittitle->getText();
        $topic->content = $this->editform->editcontent->getText();
        $topic->ispublic = $this->editform->editispublic->isChecked();
        if(strlen($topic->title)==0){
            $this->setError('не введен заголовое');
            return;
        }
        $topic->save();
        $tags = $this->editform->edittags->getTags();
        $topic->saveTags($tags);
        // $this->topiclist->setSelectedRow($topic->topic_id);

        $nodeid = $this->tree->selectedNodeId();
        if ($this->_edited == 0) {
            $topic->addToNode($nodeid);
        }
         $this->ReloadTopic($nodeid);


        $this->_tvars['editor'] = false;


        //$this->ReloadTree();
    }

    public function onTopicCancel($sender) {
        $this->_edited = 0;
        $this->_tvars['editor'] = false;
    }

    //вырезать узел  в клипборд
    public function onNodeCut($sender) {
        $this->clipboard[0] = $this->tree->selectedNodeId();
        ;
        $this->clipboard[1] = 'node';
    }

    ///удалить узел
    public function onNodeDelete($sender) {
        $id = $this->tree->selectedNodeId();

        Node::delete($id);
        Topic::deleteByNode($id);
        $this->ReloadTree();
        $this->ReloadTopic(-1);
        $this->tree->selectedNodeId(-1);
        ;
    }

    //вставить узел
    public function onNodePaste($sender) {
        if ($this->clipboard[1] == 'node') {
            $dest = Node::load($this->tree->selectedNodeId());

            if ($this->clipboard[0] == $dest) {
                return;
            }
            $node = Node::load($this->clipboard[0]);
            if (strpos($dest->mpath, $node->mpath) === 0) {
                $this->setError("Нельзя  переместить в своего наследника");
                return;
            }

            $node->moveTo($dest->node_id);
            $this->ReloadTree();
            $this->clipboard = array();
        }
    }

    //сохранить  узел после  редактирования
    public function OnNodeTitle($form) {

        $op = $form->opname->getText();
        $id = $this->tree->selectedNodeId();
        if ($op == 'add') {
            $parent = Node::load($id);
            $node = new Node();
            $node->pid = $id;
            $node->user_id = System::getUser()->user_id;
            $node->title = $form->editnodetitle->getText();
            $node->save();
            $this->ReloadTopic($node->node_id);
        }
        if ($op == 'edit') {
            $node = Node::load($id);
            $node->title = $form->editnodetitle->getText();
            $node->save();
        }
        // $form->editnodetitle->setText('');

        $this->ReloadTree();
        $this->tree->selectedNodeId($node->node_id);
    }

    //загрузить дереаво
    public function ReloadTree() {

        $this->tree->removeNodes();
        $user_id = System::getUser()->user_id;


        $itemlist = Node::find('user_id=' . $user_id, "pid,mpath,title");
        if (count($itemlist) == 0) { //добавляем  корень
            $root = new Node();
            $root->title = "//";
            $root->user_id = $user_id;
            $root->save();

            $itemlist = Node::find('user_id=' . $user_id, "pid,mpath,title");
        }
        $first = null;
        $nodelist = array();
        foreach ($itemlist as $item) {
            $node = new \ZCL\BT\TreeNode($item->title, $item->node_id);
            $node->tag = $item->tcnt;  //количество  топиков в ветке
            $parentnode = $nodelist[$item->pid] ?? null;

            $this->tree->addNode($node, $parentnode);

            $nodelist[$item->node_id] = $node;
            if ($first == null)
                $first = $node;
        }
    }

    // загрузить список  топиков  для  выбранного узла
    public function ReloadTopic($nodeid = 0) {
        if ($nodeid == 0)
            $nodeid = $this->tree->selectedNodeId();

        $this->_tarr = Topic::findByNode($nodeid);
        $this->topiclist->Reload();
    }

    //клик по  узлу
    public function onTree($sender, $id) {
        $this->_edited = 0;
        $this->_tvars['editor'] = false;
        $this->topiclist->setSelectedRow();
        $this->ReloadTopic($id);
        
        $this->_sarr = array();
        $this->searchlist->Reload();
         
        $this->sform->skeyword->setText('');
          
    }

    //вывод строки  списка  топиков

    public function onRow($row) {
        $topic = $row->getDataitem();
        $row->add(new Label('title', $topic->title));
        //$row->add(new ClickLink('title', $this,'onTopic'));
        $fav = $row->add(new Label('fav'));
        $fav->setVisible($topic->favorites > 0);
   
    }

    //клик по топику
    public function onTopic($row) {

       $topic = $row->getDataItem(); 
       $this->_farr = \App\Entity\File::findByTopic($topic->topic_id);
       $this->filelist->Reload();
       $this->topiclist->setSelectedRow($row);
       $this->topiclist->Reload();
    }

    //избранное
    public function onFav($sender) {
        $topic = Topic::load($this->topiclist->getSelectedRow()->getDataItem()->topic_id);
 
        $topic->favorites = $topic->favorites == 1 ? 0 : 1;
        $topic->save();
        //$this->ReloadTopic($this->tree->selectedNodeId());
        $this->ReloadTopic();
    }

    //вырезать топик в  клипборд
    public function onTopicCut($sender) {
        $this->clipboard[0] = $this->topiclist->getSelectedRow()->getDataItem()->topic_id;
        $this->clipboard[1] = 'topic';
        $this->clipboard[2] = 'cut';
        $this->clipboard[3] = $this->tree->selectedNodeId();
    }

    //копировать шорткат  на  топик
    public function onTopicTag($sender) {
        $this->clipboard[0] = $this->topiclist->getSelectedRow()->getDataItem()->topic_id;
        $this->clipboard[1] = 'topic';
        $this->clipboard[2] = 'tag';
        $this->clipboard[3] = $this->tree->selectedNodeId();
    }

    //копировать топик
    public function onTopicCopy($sender) {
        $this->clipboard[0] = $this->topiclist->getSelectedRow()->getDataItem()->topic_id;
        $this->clipboard[1] = 'topic';
        $this->clipboard[2] = 'copy';
        $this->clipboard[3] = $this->tree->selectedNodeId();
    }

    //удалить топик
    public function onTopicDelete($sender) {
        Topic::delete($this->topiclist->getSelectedRow()->getDataItem()->topic_id);
        $this->topiclist->setSelectedRow();
        $this->ReloadTopic($this->tree->selectedNodeId());
        $this->ReloadTree();
    }

    //вставить   в  узел топик  или  шорткат
    public function onTopicPaste($sender) {
        if ($this->clipboard[1] != 'topic')
            return;


        $topic = Topic::load($this->clipboard[0]);
        if ($this->clipboard[2] == 'cut') {

            $topic->removeFromNode($this->clipboard[3]);
            $topic->addToNode($this->tree->selectedNodeId());
            $this->clipboard = array();
        }
        if ($this->clipboard[2] == 'copy') {
            $newtopic = new Topic();
            $newtopic->title = $topic->title;
            if ($this->tree->selectedNodeId() == $this->clipboard[3]) {
                $newtopic->title = $topic->title . " (копия)";
            }
            $newtopic->content = $topic->content;
            $newtopic->save();
            $newtopic->addToNode($this->tree->selectedNodeId());
        }
        if ($this->clipboard[2] == 'tag') {
            $topic->addToNode($this->tree->selectedNodeId());
        }

        $this->ReloadTopic($this->tree->selectedNodeId());
        $this->ReloadTree();
        App::$app->setReloadPage();
    }

    //аплоад файла
    public function OnFile($form) {
        $file = $form->editfile->getFile();
        if (strlen($file['tmp_name']) > 0) {
            if (filesize($file['tmp_name']) / 1024 / 1024 > 10) {

                $this->setError("Файл cлишком  большой");
                return;
            }
        } else
            return;

        $f = new \App\Entity\File();
        $f->content = file_get_contents($file['tmp_name']);
        $f->topic_id = $this->topiclist->getSelectedRow()->getDataItem()->topic_id;
        ;
        $imagedata = @getimagesize($file['tmp_name']);
        if (is_array($imagedata)) {
            $f->mime = $imagedata['mime'];
        }
        $f->size = filesize($file['tmp_name']);
        $f->filename = $file['name'];
        // $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $f->save();

        $this->_farr = \App\Entity\File::findByTopic($f->topic_id);
        $this->filelist->Reload();
    }

    public function onFileRow($row) {
        $file = $row->getDataItem();
        $row->add(new ClickLink("filedel", $this, "onFileDel"));
        $row->add(new BookmarkableLink("filelink", "/loadfile.php?id=" . $file->file_id))->setValue($file->filename);
    }

    public function onFileDel($sender) {
        $file = $sender->getOwner()->getDataItem();
        \App\Entity\File::delete($file->file_id);
        $this->_farr = \App\Entity\File::findByTopic($file->topic_id);
        $this->filelist->Reload();
    }

    //обработчик поиска
    public function OnSearch($form) {
        $text = $form->skeyword->getText();
        $t = $form->searchtype->getValue();
        if ($text == "") {
            $this->setError('Enter text!');
            return;
        }

        $this->_sarr = TopicNode::searchByText($text, $t, $form->searchtitle->isChecked());
        $this->searchlist->Reload();
    }

    //обработчик  поиска  по тегу
    public function OnTagList($sender) {
        $text = $sender->getSelectedValue();
        $this->_sarr = TopicNode::searchByTag($text);
        $this->searchlist->Reload();
    }

    //обработчик  поиска  избранных
    public function onSearchFav($sender) {

        $this->_sarr = TopicNode::searchFav();
        $this->searchlist->Reload();
    }

    //вывод строки  списка  поиска
    public function onSearchRow($row) {
        $item = $row->getDataitem();


        $row->add(new Label('stitle', $item->title));
        $row->add(new Label('snodes', $item->nodes()));
    }

    //выбор  строки  из  результата  поиска
    public function onSearchTopic($row ) {

        $topic= $row->getDataItem()  ;
        $this->tree->selectedNodeId(intval($topic->node_id));
   
        $this->ReloadTopic($topic->node_id);
        $trows = $this->topiclist->getDataRows() ;
        foreach($trows as $tr) {
            $t = $tr->getDataItem();
            if($t->topic_id==$topic->topic_id) {
                 $this->onTopic($tr) ;
            }
        }
        
        $this->searchlist->setSelectedRow($row);        
        $this->searchlist->Reload(false); 
    }

    /**
     * @see WebPage
     * 
     */
    protected function beforeRender() {
        parent::beforeRender();

        $nodeid = $this->tree->selectedNodeId();
        $node = Node::load($nodeid);
        $topicid=0;
        $row =$this->topiclist->getSelectedRow();
        
        if($row instanceof \Zippy\Html\DataList\DataRow){
           $topicid = $row->getDataItem()->topic_id; 
        }
        
        $topic = Topic::load($topicid);
        if($topic == false){
            $topicid =0;
        }

        $nodecp = $this->clipboard[1]??'' == 'node' ? $this->clipboard[0] ??'': 0;
        $topiccp = $this->clipboard[1] ??'' == 'topic' ? $this->clipboard[0] ??'': 0;

        $this->tpanel->setVisible(false);
        $this->treeadd->setVisible(false);
        $this->treeedit->setVisible(false);
        $this->treecut->setVisible(false);
        $this->treepaste->setVisible(false);
        $this->treedelete->setVisible(false);
        $this->topicadd->setVisible(false);
        $this->topicedit->setVisible(false);
        $this->topiccut->setVisible(false);
        $this->topiccopy->setVisible(false);
        $this->topictag->setVisible(false);
        $this->topicpaste->setVisible(false);
        $this->topicdelete->setVisible(false);
        $this->topiclink->setVisible(false);
        $this->tpanel->setfav->setVisible(false);
        $this->tpanel->addfile->setVisible(false);

        if ($nodeid > 0) {   //есть выделенный узел
            $this->treeadd->setVisible(true);
            $this->topicadd->setVisible(true);
            $this->treeedit->setVisible(true);

            if ($nodecp > 0) {
                $this->treepaste->setVisible(true);
            }

            if ($node->pid > 0) {   //не корень
                $this->treecut->setVisible(true);
                $this->treedelete->setVisible(true);
            }
        }

        if ($topiccp > 0 && $nodeid > 0) {
            $this->topicpaste->setVisible(true);
        }

        $this->content->setText('');
        $this->tpanel->taglist->Clear();
        if ($topicid > 0) {
            $this->tpanel->setVisible(true);
            $this->content->setText($topic->content, true);

            $this->topicedit->setVisible(true);
            $this->topiccut->setVisible(true);
            $this->topiccopy->setVisible(true);
            $this->topictag->setVisible(true);
            $this->topicdelete->setVisible(true);
              if ($topic->ispublic > 0) {
            $this->topiclink->setVisible(true);
            $this->topiclink->setLink("/topic/" . $topicid);
              }
            $this->tpanel->addfile->setVisible(true);
            ;
            $this->tpanel->setfav->setVisible(true);
            ;
            if ($topic->favorites > 0) {
                $this->tpanel->setfav->setAttribute("style", "color:brown;");
            } else {
                $this->tpanel->setfav->setAttribute("style", "color:gray;");
            }

            $tags = $topic->getTags();
            foreach ($tags as $tag) {
                $this->tpanel->taglist->addClickLink($tag, $tag);
            }
        }

        if ($topiccp > 0) {
            if ($this->clipboard[2] != 'copy' && $this->clipboard[3] == $this->tree->selectedNodeId()) {
                //в  ту  же  ветку  можно только  копировать
                $this->topicpaste->setVisible(false);
            }
        }
    }

}
