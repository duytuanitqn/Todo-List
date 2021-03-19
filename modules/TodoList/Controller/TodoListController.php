<?php

namespace Modules\TodoList\Controller;

use Core\Controller\BaseController;

class TodoListController extends BaseController
{ 
    /**
     * Display todo list
     *
     * @return mixed
     */
    public function index()
    {
        try {
            $this->loadView("TodoList\Views\index.html");
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }
} 