<?php

namespace App\Pages;

use \App\System\Application as App;
use \App\System\System;
use \App\Entity\User;
use \App\Entity\Blog;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\DataList\DataView;
use \ZCL\DB\EntityDataSource;
use \App\Entity\Comment;
use \Zippy\Html\Link\BookmarkableLink;

class BlogArticle extends Base
{

    protected $_blog;

    public function __construct($id) {
        parent::__construct();

        $item = Blog::load($id);
        if ($item == null) {
            App::RedirectHome();
        }
        $this->_blog = $item;

        $this->add(new Label("title", $item->title));
        $this->add(new Label("subtitle", $item->subtitle));
        $this->add(new Label("content", $item->content, true));
        $this->add(new \Zippy\Html\Image("image", "/images/" . $item->titleimage));

        $this->add(new ClickLink("edita"))->onClick($this, "onEdit");
        $this->add(new ClickLink("dela"))->onClick($this, "onDel");
        $user = System::getUser();

        $this->edita->setVisible($user->userrole == User::ROLE_ADMIN);
        $this->dela->setVisible($user->userrole == User::ROLE_ADMIN);

        //$this->add(new \App\Blocks\BArticles('barticles'));


        $this->add(new Label('count', '0'));
        $this->datalist = $this->add(new DataView("commentitem", new CommentListDataSource($this->_blog->blog_id), $this, 'OnAddRow'));
        $this->datalist->Reload();

        $form = $this->add(new \Zippy\Html\Form\Form('commentform'));

        $form->add(new \Zippy\Html\Form\TextArea('comment'));
        $form->onSubmit($this, 'OnComment');
        $form->add(new \ZCL\Captcha\Captcha('captcha'));
        $form->add(new \Zippy\Html\Form\TextInput('code'));
        $form->add(new \Zippy\Html\Form\TextInput('cname'));

        $datalist = $this->add(new DataView("rarticlelist", new EntityDataSource("\\App\\Entity\\Blog", "blog_id<>" . $id, "rand()", 3), $this, 'OnAddRandRow'));
        $datalist->Reload();
    }

    public function onEdit($sender) {

        App::Redirect("\\App\\Pages\\EditBlog", $this->_blog->blog_id);
    }

    public function onDel($sender) {
        Blog::delete($this->_blog->blog_id);
        App::Redirect("\\App\\Pages\\BlogList");
    }

    public function OnComment($sender) {
        $user = System::getUser();
        $content = $this->commentform->comment->getText();
        if (strlen($content) == 0) {

            App::$app->getResponse()->addJavaScript("window.location='#commentform'", true);
            return;
        }

        if ($user->user_id == 0) {
            if (!$this->commentform->captcha->checkCode($this->commentform->code->getText())) {
                $this->setError("Wrong code");
                $this->commentform->code->setText('');
                $this->commentform->captcha->Refresh();
                return;
            }
        }
        $comment = new Comment();
        $comment->content = $content;

        if ($user->user_id > 0) {
            $comment->author = $user->username;
        } else {
            $comment->author = $this->commentform->cname->getText() . " (guest)";
        }

        ;
        //  $comment->content = str_replace('\n','<br/>',$comment->content);
        //  $comment->content = str_replace(array('\n','\r'),'',nl2br($comment->content));
        $comment->createdon = time();

        $comment->blog_id = $this->_blog->blog_id;
        $comment->save();

        $this->goAnkor('comments');
        $this->commentform->comment->setText('');
        $this->commentform->code->setText('');
        $this->commentform->captcha->Refresh();
        $this->datalist->Reload();
    }

    public function OnAddRow($datarow) {
        $item = $datarow->getDataItem();


        $datarow->add(new \Zippy\Html\Label("created", date('Y-m-d H:i', $item->createdon)));
        if ($item->moderated == 1) {
            $item->content = "Canceled by moderator";
        }
        $datarow->add(new \Zippy\Html\Label("content2", $item->content));
        $datarow->add(new \Zippy\Html\Label("author", $item->author));
        $datarow->add(new \Zippy\Html\Link\ClickLink('deletecomment', $this, 'OnDeleteComment'))->SetVisible(System::getUser()->username == "admin" && $item->moderated != 1);
    }

    public function OnDeleteComment($sender) {
        $comment = $sender->owner->getDataItem();
        $comment->moderated = 1;
        $comment->Save();
        $this->goAnkor($comment->comment_id);
        $this->datalist->Reload();
        $this->goAnkor('commentanchor');
    }

    protected function beforeRender() {
        parent::beforeRender();
        $count = Comment::findCnt('blog_id =' . $this->_blog->blog_id);
        $this->getComponent('count')->setText($count);
    }

    public function OnAddRandRow($datarow) {

        $item = $datarow->getDataItem();
        $datarow->add(new BookmarkableLink("rtitle", "/blog/" . $item->blog_id))->setValue($item->title);
        $datarow->add(new BookmarkableLink("rphoto", "/blog/" . $item->blog_id))->setValue("/images/" . $item->titleimage);
        ;
    }

}

class CommentListDataSource implements \Zippy\Interfaces\DataSource
{

    private $blog_id = 0;

    public function __construct($blog_id) {
        $this->blog_id = $blog_id;
    }

    public function getItemCount() {
        return Comment::findCnt('blog_id =' . $this->blog_id);
    }

    public function getItems($start, $count, $sortfield = null, $asc = null) {
        return Comment::find('blog_id =' . $this->blog_id, 'createdon  desc');
    }

    public function getItem($id) {
        
    }

}
