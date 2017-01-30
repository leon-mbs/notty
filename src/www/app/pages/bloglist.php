<?php

namespace App\Pages;

use \Zippy\Html\DataList\DataView;
use \ZCL\DB\EntityDataSource;
use \Zippy\Html\Link\BookmarkableLink;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \App\System\Application as App;
use \App\System\System;
use \App\Entity\User;
use \App\Entity\Blog;
use \App\System\Filter;
use \Zippy\Html\Image;

class BlogList extends Base
{

    public function __construct() {
        parent::__construct();


        $this->_datalist = $this->add(new DataView("articlelist", new EntityDataSource("\\App\\Entity\\Blog", "", "createdon desc"), $this, 'OnAddRow'));
        $this->_datalist->setPageSize(12);
        $this->add(new \Zippy\Html\DataList\Paginator("pag", $this->_datalist));
        $this->_datalist->Reload();
        $user = System::getUser();
        $this->add(new ClickLink("newa", $this, "onNew"))->setVisible($user->userrole == User::ROLE_ADMIN);


        $datalist = $this->add(new DataView("barticlelist", new EntityDataSource("\\App\\Entity\\Blog", "better=1", "createdon desc", 4), $this, 'OnAddBetterRow'));
        $datalist->Reload();
    }

    public function OnAddRow($datarow) {
        $item = $datarow->getDataItem();
        $datarow->add(new BookmarkableLink("photo", "/blog/" . $item->blog_id))->setValue("/images/" . $item->titleimage);


        $datarow->add(new BookmarkableLink("title", "/blog/" . $item->blog_id))->setValue($item->title);
        $user = System::getUser();

        $datarow->add(new Label("subtitle", $item->subtitle));
        $datarow->add(new ClickLink("edita", $this, "onEdit"))->setVisible($user->userrole == User::ROLE_ADMIN);
        $datarow->add(new ClickLink("dela", $this, "onDel"))->setVisible($user->userrole == User::ROLE_ADMIN);
    }

    public function onNew($sender) {
        App::Redirect("\\App\\Pages\\EditBlog");
    }

    public function onEdit($sender) {
        $item = $sender->getOwner()->getDataItem();
        App::Redirect("\\App\\Pages\\EditBlog", $item->blog_id);
    }

    public function onDel($sender) {
        $item = $sender->getOwner()->getDataItem();
        Blog::delete($item->blog_id);
        $this->_datalist->Reload();
        $this->barticlelist->Reload();
    }

    public function OnAddBetterRow($datarow) {

        $item = $datarow->getDataItem();
        $datarow->add(new BookmarkableLink("barticle", "/blog/" . $item->blog_id));

        $datarow->barticle->add(new Label("btitle", "<img src=\"/images/{$item->titleimage}\" style=\"width:64px; float:right;msrgin:2px;\">" . $item->title, true));
        //$datarow->barticle->add(new Image("bimg"))->setUrl("/images/". $item->titleimage);
        ;
    }

}
