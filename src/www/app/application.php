<?php

namespace App;

/**
 * Класс  приложения, выполняющий
 * жизненный  цикл  работы  сайта
 */
class Application extends \Zippy\WebApplication
{

    public function __construct($homepage)
    {
        parent::__construct($homepage);

        $this->set404('templates/404.html');
    }

    /**
     * Возвращает  шаблон  страницы
     *
     * @param mixed $name
     * @param mixed $layout
     */
    public function getTemplate($name, $layout = '')
    {

        $path = '';
        $name = ltrim($name, '\\');
        $arr = explode('\\', $name);
        $templatepath = _ROOT . 'templates/app/';


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
    public function Route($uri)
    {


        if (preg_match('/^[-#a-zA-Z0-9\/_]+$/', $uri) == 0) {
            // new \Zippy\Exception('Invalid URI: ' . $uri);
            Application::Redirect404();
        }

        $pages = array(
            "signin" => "\\App\\Pages\\UserLogin",
            "signup" => "\\App\\Pages\\Registration",
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
            if ($arr[1] > 0 && $arr[0] == 'topic') {
                $this->LoadPage("\\App\\Pages\\ShowTopic", $arr[1]);
            } else
                $this->getResponse()->to404Page();
        }
    }

    /**
     * Редирект на  страницу с  ошибкой
     *
     */
    public static function RedirectError($message)
    {
        self::$app->getResponse()->Redirect("\\App\\Pages\\Error", $message);
        // Application::Redirect404();
    }

    /**
     * Редирект непосредственно  по  адресу
     *
     */
    public static function toPage($url)
    {
        self::$app->getResponse()->toPage($url);
        self::$app->getResponse()->output();
    }

}
