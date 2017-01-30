<?php

namespace App\Pages;

use \App\System\Application as App;
use \App\System\System;
use \App\Entity\User;
use \App\Entity\Blog;
use \Zippy\Html\Label;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Form\File;

class EditBlog extends Base
{

    protected $_blog;

    public function __construct($id = 0) {
        parent::__construct();

        $this->_blog = Blog::load($id);
        if ($this->_blog == null) {
            $this->_blog = new Blog();
        }

        $this->add(new Form('editform'))->onSubmit($this, 'formOnSubmit');
        $this->editform->add(new TextInput('title'))->setText($this->_blog->title);
        $this->editform->add(new TextInput('subtitle'))->setText($this->_blog->subtitle);
        $this->editform->add(new TextArea('content'))->setText($this->_blog->content);
        $this->editform->add(new File('image'));
        $this->editform->add(new \Zippy\Html\Image('preview', '/images/' . $this->_blog->titleimage));
        $this->editform->add(new CheckBox('better'))->setValue($this->_blog->better);
    }

    public function formOnSubmit($sender) {

        $this->_blog->title = $this->editform->title->getText();
        $this->_blog->subtitle = $this->editform->subtitle->getText();
        $this->_blog->content = $this->editform->content->getText();
        $this->_blog->better = $this->editform->better->getValue();

        $file = $this->editform->image->getFile();
        if (strlen($file['tmp_name']) > 0) {

            $imagedata = @getimagesize($file['tmp_name']);
            if ($imagedata == false) {
                $this->setError('Неверное   изображение');
                return;
            }
            if ($imagedata[0] != $imagedata[1]) {
                //  $this->setError('Image must be square');
                //  $logoerror = true;
            }

            $image = new \App\Entity\Image();
            $image->content = file_get_contents($file['tmp_name']);
            $image->mime = $imagedata['mime'];
            $image->save();
            $this->_blog->titleimage = $image->image_id;
        }
        $this->_blog->save();
        App::Redirect("\\App\\Pages\\BlogList");
    }

}
