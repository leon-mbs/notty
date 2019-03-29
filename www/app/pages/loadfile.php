<?php

namespace App\Pages;

 

class LoadFile extends Base
{

    public function __construct($file_id) {
        parent::__construct();

        $file = \App\Entity\File::load($file_id);
        
        $topic = \App\Entity\Topic::load($file->topic_id);
        
        if($topic->ispublic <> 1){
            $user = \App\System::getUser();
            if ($user->user_id != $topic->user_id) {
                 App::Redirect404();
                 return;    
            }
        }        

        if (strlen($file->mime) >0) {  //картинка
            
            header("Content-Type: " . $file->mime);
        } else {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $file->filename);
            header('Content-Transfer-Encoding: binary');
        }

        header("Content-Length: " . $file->size);

        echo $file->content;
        exit;
    }

}
