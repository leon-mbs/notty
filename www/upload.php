<?php
require_once 'init.php';
  
if(isset($_FILES['upload'])){
   
     $message = '';   
       $imagedata = @getimagesize($_FILES['upload']['tmp_name']);
       if(is_array($imagedata)) {
               
    
        $filename=  $_FILES['upload']['name'];
        $f = new \ App\Entity\File();
        $f->filename = $_FILES['upload']['name'];
        $f->content = file_get_contents($_FILES['upload']['tmp_name']);
        $f->topic_id = \App\Session::getSession()->topic_id;
        
        if (is_array($imagedata)) {
            $f->mime = $imagedata['mime'];
        }
        $f->size = filesize($_FILES['upload']['tmp_name']); 
        $f->save();         
         
        $url="/files/". $f->file_id;
        
       } else {
         $message ="Неверное  изображение!"; 
  
             
       }
      echo "<script type='text/javascript'> window.parent.CKEDITOR.tools.callFunction(".$_GET['CKEditorFuncNum'].", '$url', '$message');</script>";
    
     
}
  