<?php

namespace Core\Traits;

trait View {
    
    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public function render($path, $args = [])
    {
        try {
            extract($args, EXTR_SKIP);
            $file = dirname(dirname(__DIR__)) ."/". $path;  // relative to Core directory
            if (is_readable($file)) {
                require $file;
            } else {
                throw new \Exception("$file not found");
            }
        } catch(Exception $e) {
                echo 'Message: ' .$e->getMessage();
        }
    }
    
    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public function loadView($template, $args= [])
    {
        try {
            static $twig = null;
            if ($twig === null) {
                $loader = new \Twig_Loader_Filesystem(dirname(dirname(__DIR__))."/modules");
                $twig = new \Twig_Environment($loader);
            }
            echo $twig->render($template, $args);
        } catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }
}
