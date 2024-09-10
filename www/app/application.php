<?php

namespace App;

/**
 * Класс  приложения, выполняющий
 * жизненный  цикл  работы  сайта
 */
class Application extends \Zippy\WebApplication
{
  
    /**
     * Возвращает  шаблон  страницы
     *
     * @param mixed $name
     * @param mixed $layout
     */
    public function getTemplate($name, $layout = '') {

        $path = '';
        $name = ltrim($name, '\\');
        $arr = explode('\\', $name);
        $templatepath = _ROOT . 'templates/';


        //    $path = \App\getTemplate($templatepath . $lang . '/', $name, $layout);
        $className = str_replace("\\", "/", ltrim($name, '\\'));

        if (strpos($className, 'App/') === 0) {
            $path = $templatepath . (str_replace("App/", "", $className)) . ".html";
        }



        if (file_exists(strtolower($path)) == false) {
            throw new \App\Exception('Invalid template path: ' . strtolower($path));
        }
        $template = @file_get_contents(strtolower($path));

        return $template;
    }

    /**
     * Роутер.  Вызывает  соответствующие  функции  для  модулей
     *
     * @param mixed $uri
     */
    public function Route($uri) {


        if (preg_match('/^[-#a-zA-Z0-9\/_]+$/', $uri) == 0) {
            // new \Zippy\Exception('Invalid URI: ' . $uri);
            Application::Redirect404();
        }

        $pages = array(
            "topic" => "\\App\\Pages\\ShowTopic",
            "signin" => "\\App\\Pages\\UserLogin",
           // "files" => "\\App\\Pages\\LoadFile",
            "main" => "\\App\\Pages\\Main");

        if ($uri == '')
            $uri = 'main';

        $arr = explode('/', $uri);

        if ($arr[1] > 0) {
            $this->LoadPage($pages[$arr[0]], $arr[1]);
        } else
        if ($pages[$uri] != null) {
            $this->LoadPage($pages[$uri]);
        } else {
     
                $this->getResponse()->to404Page();
        }
    }

    /**
     * Редирект на  страницу с  ошибкой
     *
     */
    public static function RedirectError($message) {
        self::$app->getResponse()->Redirect("\\App\\Pages\\Error", $message);
        // Application::Redirect404();
    }
           
  

}
