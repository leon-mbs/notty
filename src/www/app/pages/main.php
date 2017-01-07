<?php

namespace App\Pages;

use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Panel;
use \Zippy\Html\Label;
use \Zippy\Html\Image;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
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
    public $_tarr;
    public $_sarr;

    public function __construct()
    {
        parent::__construct();
        $tree = $this->add(new Tree("tree"));
        $tree->onSelectNode($this, "onTree");

        $this->ReloadTree();


        $this->add(new Form("nodeform"))->onSubmit($this, "OnNodeTitle");
        $this->nodeform->add(new TextInput("editnodetitle"));
        $this->nodeform->add(new TextInput("opname"));


        $this->add(new ClickLink("treeadd"));
        $this->add(new ClickLink("treeedit"));
        $this->add(new ClickLink("treecut", $this, 'onNodeCut'));
        $this->add(new ClickLink("treepaste", $this, 'onNodePaste'));
        $this->add(new ClickLink("treedelete", $this, 'onNodeDelete'));



        $this->add(new ClickLink("topicadd", $this, 'onTopicAdd'));
        $this->add(new ClickLink("topicedit", $this, 'onTopicEdit'));
        $this->add(new ClickLink("topiccut", $this, 'onTopicCut'));
        $this->add(new ClickLink("topiccopy", $this, 'onTopicCopy'));
        $this->add(new ClickLink("topictag", $this, 'onTopicTag'));
        $this->add(new ClickLink("topicpaste", $this, 'onTopicPaste'));
        $this->add(new ClickLink("topicdelete", $this, 'onTopicDelete'));
        $this->add(new BookmarkableLink("topiclink"));


        $topiclist = $this->add(new \Zippy\Html\DataList\DataView('topiclist', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\ArrayPropertyBinding($this, '_tarr')), $this, "onRow"));

        $topiclist->setCellClickEvent($this, 'onTopic');

        $topiclist->setSelectedClass('seltopic');

        $this->add(new Label("content"));


        $this->add(new Form("editform"));
        $this->editform->add(new TextInput("edittitle"));
        $this->editform->add(new \ZCL\BT\Tags("edittags"));
        $this->editform->add(new TextArea("editcontent"));
        $this->editform->add(new ClickLink("editcancel", $this, "onTopicCancel"));
        $this->editform->add(new SubmitLink("editsave"))->onClick($this, "onTopicSave");
        ;




        $this->add(new Form("sform"))->onSubmit($this, "OnSearch");
        $this->sform->add(new TextInput("skeyword"));
        $searchlist = $this->add(new \Zippy\Html\DataList\DataView('searchlist', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\ArrayPropertyBinding($this, '_sarr')), $this, "onSearchRow"));

        $searchlist->setCellClickEvent($this, 'onSearchTopic');

        $searchlist->setSelectedClass('seltopic');

        $this->add(new \Zippy\Html\Link\LinkList("taglist"))->onClick($this, 'OnTagList');


        $this->_tvars['editor'] = false;
    }

    public function onTopicAdd($sender)
    {

        $this->_edited = 0;
        $this->editform->edittitle->setText('');
        $this->editform->editcontent->setText('');

        $this->_tvars['editor'] = true;
    }

    public function onTopicEdit($sender)
    {
        $this->_edited = $this->topiclist->getSelectedRow();
        $topic = Topic::load($this->_edited);

        $this->editform->edittitle->setText($topic->title);
        $this->editform->edittags->setTags($topic->getTags());
        $this->editform->edittags->setSuggestions($topic->getSuggestionTags());
        $this->editform->editcontent->setText($topic->content);

        $this->_tvars['editor'] = true;
    }

    public function onTopicSave($sender)
    {

        $topic = $this->_edited > 0 ? Topic::load($this->_edited) : new Topic();
        $topic->title = $this->editform->edittitle->getText();
        $topic->content = $this->editform->editcontent->getText();

        $topic->save();
        $tags = $this->editform->edittags->getTags();
        $topic->saveTags($tags);
        $this->topiclist->setSelectedRow($topic->topic_id);
        $this->ReloadTopic();
        $this->_tvars['editor'] = false;
    }

    public function onTopicCancel($sender)
    {
        $this->_edited = 0;
        $this->_tvars['editor'] = false;
    }

    public function onNodeCut($sender)
    {
        $this->clipboard[0] = $this->tree->selectedNodeId();
        ;
        $this->clipboard[1] = 'node';
    }

    public function onNodeDelete($sender)
    {
        $id = $this->tree->selectedNodeId();

        Node::delete($id);
        Topic::deleteByNode($id);
        $this->ReloadTree();
        $this->ReloadTopic(-1);
        $this->tree->selectedNodeId(-1);
        ;
    }

    public function onNodePaste($sender)
    {
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

    public function OnNodeTitle($form)
    {

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

    public function ReloadTree()
    {

        $this->tree->removeNodes();
        $user_id = System::getUser()->user_id;


        $itemlist = Node::find('user_id=' . $user_id, "mpath,title");
        if (count($itemlist) == 0) { //добавляем  корень
            $root = new Node();
            $root->title = "//";
            $root->user_id = $user_id;
            $root->save();

            $itemlist = Node::find('user_id=' . $user_id, "mpath,title");
        }
        $first = null;
        $nodelist = array();
        foreach ($itemlist as $item) {
            $node = new \ZCL\BT\TreeNode($item->title, $item->node_id);
            $node->tag = $item->tcnt;  //количество  топиков в ветке
            $parentnode = @$nodelist[$item->pid];

            $this->tree->addNode($node, $parentnode);

            $nodelist[$item->node_id] = $node;
            if ($first == null)
                $first = $node;
        }
    }

    public function ReloadTopic($nodeid = 0)
    {
        if ($nodeid == 0)
            $nodeid = $this->tree->selectedNodeId();

        $this->_tarr = Topic::findByNode($nodeid);
        $this->topiclist->Reload();
    }

    public function onTree($sender, $id)
    {

        $this->topiclist->setSelectedRow(-1);
        $this->ReloadTopic($id);
    }

    public function onRow($row)
    {
        $item = $row->getDataitem();
        $row->add(new Label('title', $item->title));
    }

    public function onTopic($sender, $topic_id)
    {

        // $topic = Topic::load($topic_id);   
        // $this->content->setText($topic->content,true);
    }

    public function OnTopicTitle($form)
    {

        $topic = new Topic();

        $topic->title = $form->edittopictitle->getText();
        $topic->save();
        $this->topiclist->Reload();
        $this->topiclist->setSelectedRow($topic->topic_id);
        $nodeid = $this->tree->selectedNodeId();
        $topic->addToNode($nodeid);

        $this->ReloadTree();
        $this->ReloadTopic($nodeid);
    }

    public function onTopicCut($sender)
    {
        $this->clipboard[0] = $this->topiclist->getSelectedRow();
        $this->clipboard[1] = 'topic';
        $this->clipboard[2] = 'cut';
        $this->clipboard[3] = $this->tree->selectedNodeId();
    }

    public function onTopicTag($sender)
    {
        $this->clipboard[0] = $this->topiclist->getSelectedRow();
        $this->clipboard[1] = 'topic';
        $this->clipboard[2] = 'tag';
        $this->clipboard[3] = $this->tree->selectedNodeId();
    }

    public function onTopicCopy($sender)
    {
        $this->clipboard[0] = $this->topiclist->getSelectedRow();
        $this->clipboard[1] = 'topic';
        $this->clipboard[2] = 'copy';
        $this->clipboard[3] = $this->tree->selectedNodeId();
    }

    public function onTopicDelete($sender)
    {
        Topic::delete($this->topiclist->getSelectedRow());
        $this->ReloadTopic($this->tree->selectedNodeId());
    }

    public function onTopicPaste($sender)
    {
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

    public function OnSearch($form)
    {
        $text = $form->skeyword->getText();
        if ($text == "") {
            $this->setError('Enter text!');
            return;
        }

        $this->_sarr = TopicNode::searchByText($text);
        $this->searchlist->Reload();
    }

    public function OnTagList($sender)
    {
        $text = $sender->getSelectedValue();
        $this->_sarr = TopicNode::searchByTag($text);
        $this->searchlist->Reload();
    }

    public function onSearchRow($row)
    {
        $item = $row->getDataitem();


        $row->add(new Label('stitle', $item->title));
        $row->add(new Label('snodes', $item->nodes()));
    }

    public function onSearchTopic($sender, $tn_id)
    {

        $topic = TopicNode::load($tn_id);
        $this->tree->selectedNodeId($topic->node_id);

        $this->topiclist->setSelectedRow($topic->topic_id);
        $this->ReloadTopic($topic->node_id);
    }

    protected function beforeRender()
    {
        parent::beforeRender();
        $nodeid = $this->tree->selectedNodeId();
        $node = Node::load($nodeid);

        $topicid = $this->topiclist->getSelectedRow();
        ;
        $topic = Topic::load($topicid);


        $nodecp = $this->clipboard[1] == 'node' ? $this->clipboard[0] : 0;
        $topiccp = $this->clipboard[1] == 'topic' ? $this->clipboard[0] : 0;

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
        $this->taglist->Clear();
        if ($topicid > 0) {
            $this->content->setText($topic->content, true);
            $this->topicedit->setVisible(true);
            $this->topiccut->setVisible(true);
            $this->topiccopy->setVisible(true);
            $this->topictag->setVisible(true);
            $this->topicdelete->setVisible(true);
            $this->topiclink->setVisible(true);
            $this->topiclink->setLink("/topic/1");

            $tags = $topic->getTags();

            foreach ($tags as $tag) {
                $this->taglist->addClickLink($tag, $tag);
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
