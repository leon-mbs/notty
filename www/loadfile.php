<?php
require_once 'init.php';

        if (!is_numeric($_REQUEST['id']))
            die;

        $file = \App\Entity\File::load($_REQUEST['id']);
        
       

        if (strlen($file->mime) >0) {  //картинка
            
            header("Content-Type: " . $file->mime);
        } else {
            
            $topic = \App\Entity\Topic::load($file->topic_id);
            
            if($topic->ispublic <> 1){
                $user = \App\System::getUser();
                if ($user->user_id != $topic->user_id) {
                     App::Redirect404();
                     die;    
                }
            }             
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $file->filename);
            header('Content-Transfer-Encoding: binary');
        }

        header("Content-Length: " . $file->size);

        echo $file->content;
        die;          
        